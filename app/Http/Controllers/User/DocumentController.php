<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\DocumentVersionRepositoryInterface;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\DocumentStoreRequest;
use App\Jobs\MakeSingleFolder;
use Illuminate\Support\Facades\Storage;
use App\Jobs\MakeFolderProject;
use App\Jobs\MakeSingleFile;
use App\Jobs\UpdateLinkDocument;
use App\Jobs\DeleteSingleFile;

class DocumentController extends Controller
{
    protected $document;
    protected $project;
    protected $documentVersion;

    public function __construct(
        DocumentRepositoryInterface $document,
        ProjectRepositoryInterface $project,
        DocumentVersionRepositoryInterface $documentVersion
    ) {
        $this->document = $document;
        $this->project = $project;
        $this->documentVersion = $documentVersion;
    }

    public function create($id)
    {
        $project = $this->project->findWithRelations($id, 'documentVersions');
        $documents = $this->document->getDirInProject($id);

        return view('document.create', compact('project', 'documents'));
    }

    public function store(DocumentStoreRequest $request, $id)
    {
        $projectName = $this->project->find($id)->name;
        $parentName = $this->document->find($request->document_parent)->name;

        $file = $request->file('file');

        if (isset($file)) {
            $documentExt = $file->getClientOriginalExtension();
            $name = $request->name . '.' . $documentExt;

            $filePut = $request->file->storeAs(config('fpms.temporary_file'), $name, 'public');

            if ($documentExt == 'doc' || $documentExt == 'docx') {
                $icon = config('fpms.icon.word');
            } elseif ($documentExt == 'pptx' || $documentExt == 'ppsx') {
                $icon = config('fpms.icon.powerpoint');
            } elseif ($documentExt == 'xls' || $documentExt == 'xlsx') {
                $icon = config('fpms.icon.excel');
            } elseif ($documentExt == 'pdf') {
                $icon = config('fpms.icon.pdf');
            } elseif ($documentExt == 'sql') {
                $icon = config('fpms.icon.database');
            } elseif ($documentExt == 'jpg' || $documentExt == 'png') {
                $icon = config('fpms.icon.photo');
            } else {
                $icon = config('fpms.icon.other');
            }
        } else {
            $documentExt = null;
            $documentLink = null;
            $name = $request->name;
            MakeSingleFolder::dispatch($projectName, $parentName, $name);
            $icon = config('fpms.icon.folder');
        }
        
        $data = array_merge($request->all(), [
            'text' => $name,
            'document_ext' => $documentExt,
            'icon' => $icon,
        ]);
        
        $document = $this->document->store($data);

        if (isset($file)) {
            MakeSingleFile::dispatch($projectName, $parentName, $name);
            UpdateLinkDocument::dispatch($projectName, $name, $document->id);
        }

        Toastr::success('Document Default Successfully Created', 'Success');

        return redirect()->route('user.project.show', $id);
    }

    public function storeDefault($id)
    {
        $project = $this->project->find($id);

        MakeFolderProject::dispatch($project);
        
        $documentVersion = $this->documentVersion->store([
            'name' => $project->name . ' v1',
            'project_id' => $project->id,
        ]);

        $documentDocument = $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Document',
            'text' => 'Document',
            'document_parent' => $project->id . 'v1',
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $documentManagement = $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Management',
            'text' => 'Management',
            'document_parent' => $project->id . 'v1',
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $documentPublic = $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Public',
            'text' => 'Public',
            'document_parent' => $project->id . 'v1',
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);

        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Requirement',
            'text' => 'Requirement',
            'document_parent' => $documentDocument->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Design',
            'text' => 'Design',
            'document_parent' => $documentDocument->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Q_A',
            'text' => 'Q_A',
            'document_parent' => $documentDocument->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Test',
            'text' => 'Test',
            'document_parent' => $documentDocument->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Process',
            'text' => 'Process',
            'document_parent' => $documentDocument->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);

        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Project Member Info',
            'text' => 'Project Member Info',
            'document_parent' => $documentManagement->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Project Meeting',
            'text' => 'Project Meeting',
            'document_parent' => $documentManagement->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Report',
            'text' => 'Report',
            'document_parent' => $documentManagement->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);
        $this->document->store([
            'document_version_id' => $documentVersion->id,
            'name' => 'Progress Management',
            'text' => 'Progress Management',
            'document_parent' => $documentManagement->id,
            'document_type' => 'dir',
            'icon' => config('fpms.icon.folder'),
        ]);

        Toastr::success('Document Default Successfully Created', 'Success');

        return redirect()->back();
    }

    public function searchDocument(Request $request, $id)
    {
        $key = trim($request->data);
        
        $project = $this->project->findWithRelations($id, 'documents');
        $documents = $project->documents->filter(
            function ($item) use ($key) {
                return false !== stristr($item['name'], $key);
            }
        );
        
        return [
            'html' => view('document.search', compact('documents'))->render(),
        ];
    }

    public function destroy($id)
    {
        $document = $this->document->find($id);
        if ($document->document_type == 'dir') {
            Toastr::error('Cannot delete directory', 'Error');
        } else {
            $projectName = $document->documentVersion->project->name;
            $name = $document->text;

            DeleteSingleFile::dispatch($projectName, $name);
            $this->document->delete($id);
            Toastr::success('File Successfully Deleted', 'Success');
        }

        return redirect()->back();
    }
}
