<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{
    public function __construct(
        private KernelInterface $kernel,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file, string $targetDirectory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = substr((string) $this->slugger->slug($originalFilename), 0, 12);

        if ($safeFilename === '') {
            $safeFilename = 'file';
        }

        $uniquePart = substr(bin2hex(random_bytes(4)), 0, 8);
        $extension = $file->guessExtension() ?: 'bin';
        $fileName = $safeFilename.'-'.$uniquePart.'.'.$extension;

        try {
            $file->move($this->getTargetDirectory($targetDirectory), $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return $fileName;
    }

    public function getTargetDirectory(string $targetDirectory): string
    {
        return $this->kernel->getProjectDir().'/public/'.$targetDirectory;
    }
}
