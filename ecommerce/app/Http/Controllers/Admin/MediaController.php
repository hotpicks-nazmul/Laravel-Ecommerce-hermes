<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display media library
     */
    public function index(Request $request)
    {
        // Get ALL files recursively from the public disk
        $allFiles = Storage::disk('public')->allFiles();
        
        // Remove files in temp or cache folders if any
        $allFiles = array_filter($allFiles, function($file) {
            $folder = explode('/', $file)[0];
            return !in_array($folder, ['cache', 'tmp', 'logs']);
        });
        
        // Get file details
        $files = array_map(function ($file) {
            $fullPath = storage_path('app/public/' . $file);
            $fileInfo = [
                'path' => $file,
                'name' => basename($file),
                'url' => Storage::url($file),
                'size' => Storage::disk('public')->size($file),
                'modified' => Storage::disk('public')->lastModified($file),
                'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
                'is_image' => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']),
            ];
            
            // Get image dimensions for images
            if ($fileInfo['is_image'] && file_exists($fullPath)) {
                try {
                    $imageInfo = getimagesize($fullPath);
                    if ($imageInfo) {
                        $fileInfo['width'] = $imageInfo[0];
                        $fileInfo['height'] = $imageInfo[1];
                    }
                } catch (\Exception $e) {
                    // Ignore errors getting image dimensions
                }
            }
            
            return $fileInfo;
        }, $allFiles);
        
        // Filter by type
        $type = $request->get('type');
        if ($type && $type !== 'all') {
            $files = array_filter($files, function($file) use ($type) {
                if ($type === 'images') {
                    return $file['is_image'];
                } elseif ($type === 'videos') {
                    return in_array($file['extension'], ['mp4', 'webm', 'avi', 'mov']);
                } elseif ($type === 'documents') {
                    return in_array($file['extension'], ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']);
                }
                return true;
            });
        }
        
        // Filter by search
        $search = $request->get('search');
        if ($search) {
            $search = strtolower($search);
            $files = array_filter($files, function($file) use ($search) {
                return strpos(strtolower($file['name']), $search) !== false;
            });
        }
        
        // Sort by modification date (newest first)
        usort($files, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        // Convert to collection for pagination
        $filesCollection = collect($files);
        
        // Paginate using LengthAwarePaginator
        $perPage = request()->get('per_page', 25);
        $currentPage = request()->get('page', 1);
        $totalFiles = count($files);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($files, ($currentPage - 1) * $perPage, $perPage),
            $totalFiles,
            $perPage,
            $currentPage,
            ['path' => route('admin.media.index', [], false)]
        );
        
        return view('admin.media.index', compact('paginator'));
    }

    /**
     * Upload files
     */
    public function upload(Request $request)
    {
        $files = $request->file('files') ?? ($request->file('file') ? [$request->file('file')] : []);
        
        if (empty($files)) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
            ], 400);
        }
        
        $uploaded = [];
        $errors = [];
        
        foreach ($files as $file) {
            $maxSize = 10 * 1024;
            $allowedMimes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
                'video/mp4', 'video/webm',
                'audio/mpeg', 'audio/wav', 'audio/ogg',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ];
            
            if ($file->getSize() > $maxSize * 1024) {
                $errors[] = "{$file->getClientOriginalName()}: File size exceeds 10MB limit";
                continue;
            }
            
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                $errors[] = "{$file->getClientOriginalName()}: File type not allowed";
                continue;
            }
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $slugName = Str::slug($nameWithoutExt);
            
            $newName = $slugName . '.' . $extension;
            $counter = 1;
            while (Storage::disk('public')->exists('uploads/' . $newName)) {
                $newName = $slugName . '-' . $counter . '.' . $extension;
                $counter++;
            }
            
            $path = $file->storeAs('uploads', $newName, 'public');
            
            $uploaded[] = [
                'name' => $newName,
                'path' => $path,
                'url' => Storage::url($path),
                'size' => $file->getSize(),
            ];
        }
        
        if (empty($uploaded) && !empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $errors),
                'errors' => $errors,
            ], 422);
        }
        
        $message = count($uploaded) . ' file(s) uploaded successfully';
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'uploaded' => $uploaded,
        ]);
    }

    /**
     * Delete a file
     */
    public function destroy(Request $request)
    {
        $filePath = $request->get('path');
        
        if (!$filePath) {
            return response()->json([
                'success' => false,
                'message' => 'File path is required',
            ], 400);
        }
        
        $filePath = str_replace('..', '', $filePath);
        
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }
        
        if (Storage::disk('public')->directoryExists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete directory through this endpoint',
            ], 400);
        }
        
        Storage::disk('public')->delete($filePath);
        
        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully',
        ]);
    }

    /**
     * Get file details (AJAX)
     */
    public function show(Request $request)
    {
        $filePath = $request->get('path');
        
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }
        
        $fullPath = storage_path('app/public/' . $filePath);
        $fileInfo = [
            'path' => $filePath,
            'name' => basename($filePath),
            'url' => Storage::url($filePath),
            'size' => Storage::disk('public')->size($filePath),
            'size_formatted' => $this->formatFileSize(Storage::disk('public')->size($filePath)),
            'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($filePath)),
            'extension' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
            'is_image' => in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']),
        ];
        
        if ($fileInfo['is_image'] && file_exists($fullPath)) {
            try {
                $imageInfo = getimagesize($fullPath);
                if ($imageInfo) {
                    $fileInfo['width'] = $imageInfo[0];
                    $fileInfo['height'] = $imageInfo[1];
                    $fileInfo['dimensions'] = $imageInfo[0] . ' x ' . $imageInfo[1] . ' px';
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
        
        return response()->json([
            'success' => true,
            'file' => $fileInfo,
        ]);
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
