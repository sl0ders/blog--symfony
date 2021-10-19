<?php

namespace App\Datatables;

use App\Entity\User;
use Closure;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Style;

class UserDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function getLineFormatter(): callable|Closure
    {
        return function ($row) {
            $row["fullname"] = $row["lastname"] . " " . $row["firstname"];
            return $row;
        };
    }

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
            ->add('username', Column::class, [
                'title' => $this->translator->trans("user.datatable.username", [], "BlogTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("lastname", Column::class, [
                "visible" => false
            ])
            ->add("firstname", Column::class, [
                "visible" => false
            ])
            ->add('fullname', VirtualColumn::class, [
                'title' => $this->translator->trans("user.datatable.fullname", [], "BlogTrans"),
                'searchable' => true,
                "order_column" => 'user.created_at',
                'orderable' => true,
                'search_column' => 'user.created_at',
            ])
            ->add("email", Column::class, [
                'title' => $this->translator->trans("user.datatable.email", [], "BlogTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("roles", Column::class, [
                'title' => $this->translator->trans("user.datatable.roles", [], "BlogTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("enabled", Column::class, [
                "visible" => false
            ])
            ->add(null, ActionColumn::class, [
                    'start_html' => '<div class="start_actions row" style="height: 50px; text-align: center; margin:auto;">',
                    'title' => $this->translator->trans('sg.datatables.actions.title'),
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => "admin_user_show",
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('global.text.see', [], 'BlogTrans'),
                                'class' => 'btn btn-primary btn-xs m-auto',
                                'role' => 'button'
                            ],
                        ], [
                            'route' => 'admin_user_enabled',
                            'route_parameters' => ['id' => 'id'],
                            'icon' => 'fa fa-toggle-off',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'class' => 'btn btn-danger btn-xs m-auto',
                                'role' => 'button',
                                'title' => $this->translator->trans('global.text.enabled', [], 'BlogTrans'),
                            ],
                            'render_if' => function ($row): bool {
                                return !$row['enabled'];
                            },
                        ], [
                            'route' => 'admin_user_enabled',
                            'route_parameters' => ['id' => 'id'],
                            'icon' => 'fa fa-toggle-on',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'class' => 'btn btn-primary btn-xs m-auto',
                                'role' => 'button',
                                'title' => $this->translator->trans('global.text.disabled', [], 'BlogTrans'),
                            ],
                            'render_if' => function ($row): bool {
                                return $row['enabled'];
                            },
                        ],
                    ],
                ]
            );
    }

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "post_datatable";
    }
}
