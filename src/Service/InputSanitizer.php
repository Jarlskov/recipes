<?php

declare(strict_types=1);

namespace App\Service;

class InputSanitizer
{
    /**
     * Sanitize text input by removing potentially dangerous characters
     */
    public function sanitizeText(string $input): string
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Remove excessive whitespace
        $input = preg_replace('/\s+/', ' ', $input);
        
        return $input;
    }

    /**
     * Sanitize HTML content (for rich text areas if needed)
     */
    public function sanitizeHtml(string $input): string
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Allow only safe HTML tags (if you need HTML support)
        $allowedTags = '<p><br><strong><em><u><ol><ul><li>';
        $input = strip_tags($input, $allowedTags);
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }

    /**
     * Sanitize email input
     */
    public function sanitizeEmail(string $email): ?string
    {
        // Remove null bytes
        $email = str_replace("\0", '', $email);
        
        // Trim whitespace
        $email = trim($email);
        
        // Remove any whitespace
        $email = preg_replace('/\s+/', '', $email);
        
        // Validate email format using filter_var
        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        // Validate the sanitized email
        if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
            return null; // Invalid email format
        }
        
        // Convert to lowercase for consistency
        return strtolower($sanitizedEmail);
    }

    /**
     * Sanitize filename (for future file uploads)
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove null bytes
        $filename = str_replace("\0", '', $filename);
        
        // Use basename() to strip any path components - this is the secure way
        $filename = basename($filename);
        
        // Remove potentially dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Limit length
        $filename = substr($filename, 0, 255);
        
        return $filename;
    }

    /**
     * Validate and sanitize URL
     */
    public function sanitizeUrl(string $url): ?string
    {
        // Remove null bytes
        $url = str_replace("\0", '', $url);
        
        // Trim whitespace
        $url = trim($url);
        
        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        
        // Only allow http and https protocols
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            return null;
        }
        
        return $url;
    }

    /**
     * Sanitize numeric input
     */
    public function sanitizeNumeric(string $input): ?float
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Validate as numeric
        if (!is_numeric($input)) {
            return null;
        }
        
        return (float) $input;
    }

    /**
     * Sanitize integer input
     */
    public function sanitizeInteger(string $input): ?int
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        // Validate as integer
        if (!ctype_digit($input)) {
            return null;
        }
        
        return (int) $input;
    }
}
