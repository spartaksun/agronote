<?php

namespace App\Form;

// src/Form/CustomSearchType.php

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;


class TaskSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Date Due (Ascending)' => 'date_due_asc',
                    'Date Due (Descending)' => 'date_due_desc',
                ],
                'label' => 'Sort By',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => Task::statuses(),
                'label' => 'Status',
                'required' => false,
            ])
            ->add('search', SearchType::class, [
                'label' => 'Search',
                'required' => false,
            ]);
    }
}