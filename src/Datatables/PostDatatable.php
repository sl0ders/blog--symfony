<?php

namespace App\Datatables;

use App\Entity\Post;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;

class PostDatatable extends AbstractDatatable
{

    /**
     * @inheritDoc
     */
    public function buildDatatable(array $options = [])
    {

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
