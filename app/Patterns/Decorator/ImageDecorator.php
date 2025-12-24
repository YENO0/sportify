<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;
use App\Models\EquipmentImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Decorator Pattern - Concrete Decorator for Equipment Image Management
 * Adds image upload and management features to equipment
 */
class ImageDecorator
{
    protected Equipment $equipment;
    
    // Allowed image extensions
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    // Maximum file size in bytes (5MB)
    protected int $maxFileSize = 5 * 1024 * 1024;

    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    /**
     * Get the equipment
     */
    public function getEquipment(): Equipment
    {
        return $this->equipment;
    }

    /**
     * Validate image file
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            $errors[] = "File '{$file->getClientOriginalName()}' has invalid extension. Allowed: " . implode(', ', $this->allowedExtensions);
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            $maxSizeMB = $this->maxFileSize / (1024 * 1024);
            $errors[] = "File '{$file->getClientOriginalName()}' is too large. Maximum size: {$maxSizeMB}MB";
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = "File '{$file->getClientOriginalName()}' has invalid MIME type.";
        }

        return $errors;
    }

    /**
     * Upload and attach multiple images to equipment
     */
    public function uploadImages(array $files, array $altTexts = []): array
    {
        $uploadedImages = [];
        $errors = [];

        // Validate at least one image
        if (empty($files)) {
            throw new \InvalidArgumentException('At least one image is required.');
        }

        foreach ($files as $index => $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            // Validate image
            $validationErrors = $this->validateImage($file);
            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
                continue;
            }

            try {
                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug($this->equipment->name) . '_' . time() . '_' . ($index + 1) . '.' . $extension;
                
                // Store in equipment-specific folder
                $folder = 'equipment/' . $this->equipment->id;
                $filePath = $file->storeAs($folder, $filename, 'public');

                // Create image record
                $image = EquipmentImage::create([
                    'equipment_id' => $this->equipment->id,
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'display_order' => $index,
                    'alt_text' => $altTexts[$index] ?? $this->equipment->name . ' - Image ' . ($index + 1),
                ]);

                $uploadedImages[] = $image;
            } catch (\Exception $e) {
                $errors[] = "Failed to upload '{$file->getClientOriginalName()}': " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        return $uploadedImages;
    }

    /**
     * Delete an image
     */
    public function deleteImage(EquipmentImage $image): bool
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($image->file_path)) {
                Storage::disk('public')->delete($image->file_path);
            }

            // Delete record
            return $image->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all images for the equipment
     */
    public function getImages()
    {
        return $this->equipment->images;
    }

    /**
     * Get primary image (first image)
     */
    public function getPrimaryImage(): ?EquipmentImage
    {
        return $this->equipment->images()->first();
    }
}

