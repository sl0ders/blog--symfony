<?php

namespace App\Datatables;

use App\Entity\Post;
use Closure;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Style;

class PostDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function getLineFormatter(): callable|Closure
    {
        return function ($row) {
            $post = $this->em->getRepository(Post::class)->find($row["id"]);
            $row["extract"] = substr($post->getContent(), 0, 200);
            return $row;
        };

    }

    /**
     * @inheritDoc
     * @throws Exception
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
            ->add('title', Column::class, [
                'title' => $this->translator->trans("post.datatable.title", [], "BlogTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('chapter.number', Column::class, [
                'title' => $this->translator->trans("post.datatable.chapterNumber", [], "BlogTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("extract", VirtualColumn::class, [
                'title' => $this->translator->trans("post.datatable.extract", [], "BlogTrans"),
                'searchable' => true,
                "order_column" => 'chapter.posts.created_at',
                'orderable' => true,
                'search_column' => 'chapter.posts.created_at',
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
                            'route' => "post_show",
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
                            'route' => 'admin_post_enabled',
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
                            'route' => 'admin_post_enabled',
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
        return Post::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "post_datatable";
    }
}
