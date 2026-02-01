<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WebinarsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $webinars;

    public function __construct($webinars)
    {
        $this->webinars = $webinars;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->webinars;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.id'),
            trans('admin/pages/webinars.title'),
            trans('admin/pages/webinars.course_type'),
            'Category',
            'Points',
            trans('admin/pages/webinars.teacher_name'),
            trans('admin/pages/webinars.sale_count'),
            trans('admin/pages/webinars.price'),
            'Private',
            'Slug',
            trans('admin/pages/webinars.start_date'),
            'Duration',
            trans('admin/main.status'),
            'Capacity',
            'Sales Count Number',
            'Support',
            'Certificate',
            'Downloadable',
            'Partner Instructor',
            'Subscribe',
            trans('admin/main.created_at'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($webinar): array
    {
        return [
            $webinar->id,
            $webinar->title,
            $webinar->type,
            $webinar->category->slug,
            $webinar->points,
            $webinar->teacher->full_name,
            $webinar->sales->count(),
            $webinar->price,
            $webinar->private,
            $webinar->slug,
            dateTimeFormat($webinar->start_date, 'j M Y | H:i'),
            $webinar->duration,
            $webinar->status,
            $webinar->capacity,
            $webinar->sales_count_number,
            $webinar->support,
            $webinar->certificate,
            $webinar->downloadable,
            $webinar->partner_instructor,
            $webinar->subscribe,
            dateTimeFormat($webinar->created_at, 'j M Y | H:i'),
        ];
    }
}
