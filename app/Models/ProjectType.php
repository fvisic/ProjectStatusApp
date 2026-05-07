<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'color', 'sort_order'])]
class ProjectType extends Model
{
    use HasFactory, SoftDeletes;

    // Full Tailwind class strings kept here so Vite/Tailwind JIT picks them up.
    public static array $colors = [
        'blue'   => ['badge' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-blue-300 dark:border-blue-700',     'swatch' => 'bg-blue-500'],
        'green'  => ['badge' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-green-300 dark:border-green-700',   'swatch' => 'bg-green-500'],
        'yellow' => ['badge' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 border-yellow-300 dark:border-yellow-700', 'swatch' => 'bg-yellow-400'],
        'red'    => ['badge' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-300 dark:border-red-700',         'swatch' => 'bg-red-500'],
        'purple' => ['badge' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border-purple-300 dark:border-purple-700', 'swatch' => 'bg-purple-500'],
        'pink'   => ['badge' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400 border-pink-300 dark:border-pink-700',     'swatch' => 'bg-pink-500'],
        'indigo' => ['badge' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-300 dark:border-indigo-700', 'swatch' => 'bg-indigo-500'],
        'orange' => ['badge' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border-orange-300 dark:border-orange-700', 'swatch' => 'bg-orange-500'],
        'teal'   => ['badge' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 border-teal-300 dark:border-teal-700',     'swatch' => 'bg-teal-500'],
        'cyan'   => ['badge' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 border-cyan-300 dark:border-cyan-700',     'swatch' => 'bg-cyan-500'],
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function badgeClass(): string
    {
        return self::$colors[$this->color]['badge']
            ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600';
    }

    public function swatchClass(): string
    {
        return self::$colors[$this->color]['swatch'] ?? 'bg-gray-400';
    }

    public function pdfBadgeStyle(): string
    {
        $styles = [
            'blue'   => 'background:#deebff;color:#0052cc;',
            'green'  => 'background:#e3fcef;color:#006644;',
            'yellow' => 'background:#fff0b3;color:#974f0c;',
            'red'    => 'background:#ffebe6;color:#bf2600;',
            'purple' => 'background:#eae6ff;color:#403294;',
            'pink'   => 'background:#ffecf3;color:#ae2d6b;',
            'indigo' => 'background:#e9eaf5;color:#2c3a91;',
            'orange' => 'background:#fff3e0;color:#bf6000;',
            'teal'   => 'background:#e3f9f9;color:#006064;',
            'cyan'   => 'background:#e0f7fa;color:#00838f;',
        ];
        return $styles[$this->color] ?? 'background:#f3f4f6;color:#374151;';
    }
}
