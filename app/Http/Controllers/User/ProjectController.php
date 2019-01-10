<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Http\Requests\ProjectRequestStore;
use App\Http\Requests\ProjectRequestUpdate;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use Illuminate\Http\File;
use App\Helper\FileHelper;

class ProjectController extends Controller
{
    protected $project;
    protected $user;

    public function __construct(ProjectRepositoryInterface $project, UserRepositoryInterface $user)
    {
        $this->project = $project;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = $this->project->getProjectsByUser(\Auth::id(), [
            'id',
            'name',
            'description',
            'image',
            'users_count',
        ]);

        return view('project.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = $this->user->getNormalUser(['id', 'name']);
        
        return view('project.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProjectRequestStore  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectRequestStore $request)
    {
        if (isset($request->public)) {
            $public = true;
        } else {
            $public = false;
        }
        
        $slug = str_slug($request->name);
        $image = $request->file('image');

        if (isset($image)) {
            $imageName = FileHelper::renameImage($slug, $image);

            FileHelper::makeFolder(config('fpms.project_img'));

            $postImage = Image::make($image)->resize(900, 600)->save();
            FileHelper::saveImage(config('fpms.project_img_dir') . $imageName, $postImage);

            $imagePath = FileHelper::getPath(config('fpms.project_img_dir'), $imageName);
        } else {
            $imagePath = 'default.png';
        }

        $data = array_merge($request->all(), [
            'image' => $imagePath,
            'public' => $public,
        ]);

        $project = $this->project->store($data);

        $this->project->attachPositionUser(
            $project->id,
            $request->productowners,
            config('fpms.project_position.product_owner')
        );
        $this->project->attachPositionUser(
            $project->id,
            $request->scrummasters,
            config('fpms.project_position.scrum_master')
        );
        $this->project->attachPositionUser(
            $project->id,
            $request->techleaders,
            config('fpms.project_position.tech_leader')
        );
        $this->project->attachPositionUser(
            $project->id,
            $request->teammembers,
            config('fpms.project_position.team_member')
        );
        $this->project->attachPositionUser(
            $project->id,
            $request->stackholders,
            config('fpms.project_position.stackholder')
        );

        Toastr::success('Project Successfully Created', 'Success');
        
        return redirect()->route('user.project.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = $this->project->findWithRelations($id, 'documents');
        
        $productowners = $this->project->getUsersOfProjectWithPosition(
            $id,
            config('fpms.project_position.product_owner')
        );
        $scrummasters = $this->project->getUsersOfProjectWithPosition(
            $id,
            config('fpms.project_position.scrum_master')
        );
        $techleaders = $this->project->getUsersOfProjectWithPosition(
            $id,
            config('fpms.project_position.tech_leader')
        );
        $teammembers = $this->project->getUsersOfProjectWithPosition(
            $id,
            config('fpms.project_position.team_member')
        );
        $stackholders = $this->project->getUsersOfProjectWithPosition(
            $id,
            config('fpms.project_position.stackholder')
        );
        $data = compact('project', 'productowners', 'scrummasters', 'techleaders', 'teammembers', 'stackholders');

        return view('project.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = $this->project->with('users')->find($id);
        if (\Auth::user()->can('update', $project)) {
            $users = $this->user->getNormalUser(['id', 'name']);

            return view('project.edit', compact('project', 'users', 'members'));
        } else {
            Toastr::error('You dont have permission', 'Error');

            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\ProjectRequestUpdate $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRequestUpdate $request, $id)
    {
        $project = $this->project->with('users')->find($id);
        if (\Auth::user()->can('update', $project)) {
            if (isset($request->public)) {
                $public = true;
            } else {
                $public = false;
            }
            $slug = str_slug($request->name);
            $image = $request->file('image');

            if (isset($image)) {
                $imageName = FileHelper::renameImage($slug, $image);

                FileHelper::makeFolder(config('fpms.project_img'));

                $postImage = Image::make($image)->resize(900, 600)->save();
                FileHelper::saveImage(config('fpms.project_img_dir') . $imageName, $postImage);
                $imagePath = FileHelper::getPath(config('fpms.project_img_dir'), $imageName);

                $arrImagePath = explode('/', $project->image);
                $oldImage = end($arrImagePath);
                FileHelper::deleteOldFile(config('fpms.project_img_dir'), $oldImage);
            } else {
                $imagePath = $project->image;
            }

            $data = array_merge($request->all(), [
                'image' => $imagePath,
                'public' => $public,
            ]);
            
            $project = $this->project->update($id, $data);

            $project->users()->detach();
            $this->project->attachPositionUser(
                $project->id,
                $request->productowners,
                config('fpms.project_position.product_owner')
            );
            $this->project->attachPositionUser(
                $project->id,
                $request->scrummasters,
                config('fpms.project_position.scrum_master')
            );
            $this->project->attachPositionUser(
                $project->id,
                $request->techleaders,
                config('fpms.project_position.tech_leader')
            );
            $this->project->attachPositionUser(
                $project->id,
                $request->teammembers,
                config('fpms.project_position.team_member')
            );
            $this->project->attachPositionUser(
                $project->id,
                $request->stackholders,
                config('fpms.project_position.stackholder')
            );

            Toastr::success('Project Successfully Updated', 'Success');
            
            return redirect()->route('user.project.index');
        } else {
            Toastr::error('You dont have permission', 'Error');

            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
