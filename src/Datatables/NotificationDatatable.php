<?php

namespace App\Datatables;

use App\Entity\Notification;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Style;

class NotificationDatatable extends \Sg\DatatablesBundle\Datatable\AbstractDatatable
{

    /**
     * @inheritDoc
     */
    public function buildDatatable(array $options = [])
    {
        $this->language->set([
            'language_by_locale' => true,
        ]);

        $this->ajax->set([
            // send some extra example data
            'data' => ['data1' => 1, 'data2' => 2],
            // cache for 10 pages
            'pipeline' => 10
        ]);

        $this->options->set([
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'stripe_classes' => ['strip1', 'strip2', 'strip3'],
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => [0, 'asc'],
            'order_cells_top' => true,
            'search_in_non_visible_columns' => true,
        ]);

        $this->columnBuilder
            ->add('id', Column::class, [
                "visible" => false
            ])
        ->add("created_at", DateTimeColumn::class, [
            "title" => $this->translator->trans("notification.datatable.createdAt", [], "BlogTrans"),
            'default_content' => 'No value',
            'date_format' => 'L',
            'filter' => array(DateRangeFilter::class, array(
                'cancel_button' => true,
            )),
            'timeago' => true
        ])
        ->add("content", Column::class, [
            "title" => $this->translator->trans("notification.datatable.content", [], "BlogTrans")
        ]);
    }
    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return Notification::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "notification_datatable";
    }
}
