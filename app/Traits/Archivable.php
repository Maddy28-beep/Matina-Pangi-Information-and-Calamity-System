<?php

namespace App\Traits;

use App\Models\Archive;

trait Archivable
{
    /**
     * Archive this model instance
     */
    public function archive(?string $reason = null)
    {
        // Get model data and remove Laravel internal fields
        $data = $this->toArray();
        unset($data['deleted_at'], $data['created_at'], $data['updated_at']);

        // Create archive record
        Archive::create([
            'module_type' => $this->getArchiveModuleName(),
            'original_id' => $this->id,
            'title' => $this->getArchiveTitle(),
            'data' => $data,
            'reason' => $reason,
            'archived_by' => auth()->id(),
            'archived_at' => now(),
        ]);

        // Delete the original record
        $this->delete();

        return true;
    }

    /**
     * Get the module name for archive
     */
    protected function getArchiveModuleName(): string
    {
        // Override this in your model if needed
        return class_basename($this);
    }

    /**
     * Get the display title for archive
     */
    protected function getArchiveTitle(): string
    {
        // Try common title fields
        if (isset($this->name)) {
            return $this->name;
        }

        if (isset($this->full_name)) {
            return $this->full_name;
        }

        if (isset($this->title)) {
            return $this->title;
        }

        if (isset($this->calamity_name)) {
            return $this->calamity_name;
        }

        if (isset($this->household_id)) {
            return $this->household_id;
        }

        if (isset($this->resident_id)) {
            return $this->resident_id;
        }

        // Fallback to class name + ID
        return class_basename($this).' #'.$this->id;
    }
}
