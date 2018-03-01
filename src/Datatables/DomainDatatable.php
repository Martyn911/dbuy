<?php

namespace App\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\BooleanColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\MultiselectColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Editable\SelectEditable;
use Sg\DatatablesBundle\Datatable\Editable\TextEditable;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Filter\NumberFilter;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;
use Sg\DatatablesBundle\Datatable\Style;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DomainDatatable
 *
 * @package App\Datatables
 */
class DomainDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {
        $formatter = function($row) {
            $row['name'] = !empty($row['idn_name']) ? $row['idn_name'] : $row['name'];
            //$row['name'] = $row['name'] . ' test';

            return $row;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->ajax->set(array(
            // send some extra example data
            'data' => array('data1' => 1, 'data2' => 2),
            // cache for 10 pages
            'pipeline' => 10
        ));

        $this->language->set(array(
            //'cdn_language_by_locale' => true
            'language_by_locale' => true
            //'language' => 'de'
        ));

        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_3_STYLE,
            //'stripe_classes' => [ 'strip1', 'strip2', 'strip3' ],
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => [
                [
                    5, 'asc' //expiresAt
                ]
            ],
            'order_cells_top' => true,
            'page_length' => 10,
            'length_menu' => [
                10, 20, 30, 40, 50, 100
            ],
            //'global_search_type' => 'gt',
            'search_in_non_visible_columns' => false,
            'dom' => '<"top"rt><"bottom"lp><"clear">'
        ));

        $statuses = $this->em->getRepository('App:DomainStatus')->findAll();

        $this->columnBuilder
            ->add('name', Column::class, array(
                'title' => $this->translator->trans('domain.name'),
                //'data' => 'name',
                'filter' => [TextFilter::class, [
                        'cancel_button' => true,
                        //'placeholder_text' => 'Имя'
                    ]
                ]
            ))
            ->add('idn_name', Column::class, [
                'visible' => false
            ])
            ->add('dstatus.name', Column::class, array(
                'title' => $this->translator->trans('domain.status'),
                //'searchable' => true,
                'filter' => [SelectFilter::class, [
                    //'classes' => 'test',
                    //'multiple' => true,
                    //'width' => '85%',
                    'cancel_button' => true,
                    /*'select_search_types' => [
                        '' => null,
                        '2' => 'like',
                        '1' => 'eq',
                        'send_isNull' => 'isNull',
                        'send_isNotNull' => 'isNotNull'
                    ],
                    'select_options' => [
                        '' => 'Any',
                        '2' => 'Title with the digit 2',
                        '1' => 'Title with the digit 1',
                        'send_isNull' => 'is Null',
                        'send_isNotNull' => 'is not Null'
                    ],*/
                    'select_options' => ['' => 'All'] + $this->getOptionsArrayFromEntities($statuses, 'name', 'name'),
                ]],
            ))
            ->add('createdAt', DateTimeColumn::class, [
                'title' => $this->translator->trans('domain.created.at'),
                'date_format' => 'd.m.Y',
                'default_content' => ' - ',
                //'class_name' => 'text-center',
                'filter' => [DateRangeFilter::class, [
                    'cancel_button' => true,
                    ]
                ],
            ])
            ->add('modifedAt', DateTimeColumn::class, [
                'title' => 'modifedAt',
                'date_format' => 'd.m.Y',
                'default_content' => ' - ',
                'filter' => [DateRangeFilter::class, [
                    'cancel_button' => true,
                ]
                ],
            ])
            ->add('expiresAt', DateTimeColumn::class, [
                'title' => 'expiresAt',
                'date_format' => 'd.m.Y',
                'default_content' => ' - ',
                'filter' => [DateRangeFilter::class, [
                    'cancel_button' => true,
                ]
                ],
            ])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\Domain';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'domain_datatable';
    }
}