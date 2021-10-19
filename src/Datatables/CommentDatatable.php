<?php

namespace App\Datatables;

use App\Entity\Comment;
use Closure;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Style;

class CommentDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function getLineFormatter(): callable|Closure
    {
        return function ($row) {
            $comment = $this->em->getRepository(Comment::class)->find($row["id"]);
            $row["extract"] = substr($comment->getContent(), 0, 100);
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
            ->add("created_at", DateTimeColumn::class, [
                "title" => $this->translator->trans("comment.datatable.createdAt", [], "BlogTrans"),
                'default_content' => 'No value',
                'date_format' => 'L',
                'filter' => array(DateRangeFilter::class, array(
                    'cancel_button' => true,
                )),
                'timeago' => true
            ])
            ->add('post.title', Column::class, [
                'title' => $this->translator->trans("comment.datatable.postTitle", [], "BlogTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("extract", VirtualColumn::class, [
                'title' => $this->translator->trans("comment.datatable.extract", [], "BlogTrans"),
                'searchable' => true,
                "order_column" => 'comment.created_at',
                'orderable' => true,
                'search_column' => 'comment.created_at',
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
                            'route' => "admin_comment_show",
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
                            'route' => 'admin_comment_enabled',
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
                            'route' => 'admin_comment_enabled',
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
        return Comment::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "comment_datatable";
    }
}
