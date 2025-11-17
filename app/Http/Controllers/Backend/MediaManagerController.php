<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreMediaFolderRequest;
use App\Http\Requests\Backend\UpdateMediaCaptionRequest;
use App\Http\Requests\Backend\UpdateMediaFolderRequest;
use App\Http\Requests\Backend\UploadFolderMediaRequest;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\MediaFolderService;
use App\Services\MediaLibraryService;
use App\Support\Helper\MediaHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MediaManagerController extends Controller
{
    public function __construct(
        private readonly MediaFolderService $mediaFolderService,
        private readonly MediaLibraryService $mediaLibraryService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Media::class);

        $folderTree = $this->mediaFolderService->getTree();
        $flatFolders = collect($this->flattenFolderTree($folderTree));
        $selectedFolderId = $request->integer('folder');
        $filters = $request->only(['search', 'type', 'sort', 'direction']);
        $folderData = null;

        if ($selectedFolderId) {
            try {
                $folderData = $this->mediaFolderService->getFolderWithMedia($selectedFolderId, $filters, 36);
            } catch (ModelNotFoundException) {
                $selectedFolderId = null;
            }
        }

        if ($folderData) {
            $folderData['media']->getCollection()->transform(function ($item) {
                try {
                    if (empty($item->model_type) || $item->model_id == 0) {
                        $item->url = asset('storage/media/' . $item->file_name);
                        $item->thumb_url = $item->url;
                    } else {
                        $item->url = $item->getUrl();
                        $item->thumb_url = $item->hasGeneratedConversion('thumb') ? $item->getUrl('thumb') : $item->getUrl();
                    }
                } catch (\Exception $e) {
                    $item->url = asset('storage/media/' . $item->file_name);
                    $item->thumb_url = $item->url;
                }

                $item->caption = $item->getCustomProperty('caption');

                return $item;
            });
        }

        $breadcrumbs = [
            'title' => __('Media'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Media'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.media-manager.index', [
            'breadcrumbs' => $breadcrumbs,
            'folderTree' => $folderTree,
            'selectedFolderId' => $selectedFolderId,
            'folderData' => $folderData,
            'uploadLimits' => MediaHelper::getUploadLimits(),
            'flatFolders' => $flatFolders,
        ]);
    }

    public function store(StoreMediaFolderRequest $request): RedirectResponse
    {
        $this->authorize('create', Media::class);

        $folder = $this->mediaFolderService->create($request->validated());

        return redirect()
            ->route('admin.media-manager.index', ['folder' => $folder->getKey()])
            ->with('success', __('Folder created successfully.'));
    }

    public function update(UpdateMediaFolderRequest $request, MediaFolder $folder): RedirectResponse
    {
        $this->mediaFolderService->update($folder, $request->validated());

        return redirect()
            ->route('admin.media-manager.index', ['folder' => $folder->getKey()])
            ->with('success', __('Folder updated successfully.'));
    }

    public function destroy(MediaFolder $folder): RedirectResponse
    {
        abort_unless(auth()->user()?->can('media.delete'), 403);

        if ($folder->children()->exists() || $folder->media()->exists()) {
            return redirect()
                ->back()
                ->withErrors(__('Please remove child folders and media before deleting this folder.'));
        }

        $parentId = $folder->parent_id;
        $this->mediaFolderService->delete($folder);

        return redirect()
            ->route('admin.media-manager.index', ['folder' => $parentId])
            ->with('success', __('Folder deleted successfully.'));
    }

    public function upload(UploadFolderMediaRequest $request, MediaFolder $folder): RedirectResponse
    {
        $this->authorize('create', Media::class);

        $this->mediaFolderService->uploadToFolder(
            $folder,
            $request->file('files', []),
            $request->input('captions', [])
        );

        return redirect()
            ->route('admin.media-manager.index', ['folder' => $folder->getKey()])
            ->with('success', __('Files uploaded successfully.'));
    }

    public function updateMedia(UpdateMediaCaptionRequest $request, Media $media): RedirectResponse
    {
        $this->authorize('update', $media);

        if (! $media->folder_id) {
            abort(404);
        }

        $caption = $request->validated('caption');

        if ($caption === null || $caption === '') {
            $media->forgetCustomProperty('caption');
        } else {
            $media->setCustomProperty('caption', $caption);
        }

        $media->save();

        return redirect()
            ->route('admin.media-manager.index', ['folder' => $media->folder_id])
            ->with('success', __('Media details updated successfully.'));
    }

    public function destroyMedia(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        if (! $media->folder_id) {
            abort(404);
        }

        $folderId = $media->folder_id;

        $this->mediaLibraryService->deleteMedia($media->getKey());

        return redirect()
            ->route('admin.media-manager.index', ['folder' => $folderId])
            ->with('success', __('Media deleted successfully.'));
    }

    /**
     * @param  Collection<int, MediaFolder>  $folders
     * @return array<int, array{id:int,name:string,depth:int}>
     */
    private function flattenFolderTree(Collection $folders, int $depth = 0): array
    {
        $items = [];

        foreach ($folders as $folder) {
            $items[] = [
                'id' => $folder->getKey(),
                'name' => $folder->name,
                'depth' => $depth,
                'folder' => $folder,
            ];

            if ($folder->relationLoaded('children') && $folder->children->isNotEmpty()) {
                $items = array_merge($items, $this->flattenFolderTree($folder->children, $depth + 1));
            }
        }

        return $items;
    }
}
