<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-02
 * Time: 10:28
 */

/**
 * Screening type.
 */
namespace Form;

use Repository\AuditoriumRepository;
use Repository\ScreeningRepository;
use Repository\MovieListRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Screening ScreeningType.
 */
class ScreeningType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'screening_date',
            DateType::class,
            [
                'input' => 'string',
                'label' => 'label.screening_date',
                'required' => true,
                'choice_translation_domain' => true,
                'data_class' => null,
            ]
        );

        $builder->add(
            'screening_time',
            TimeType::class,
            [
                'input' => 'string',
                'label' => 'label.screening_time',
                'required' => true,
                'choice_translation_domain' => true,
                'data_class' => null,
            ]
        );


        $builder->add(
            'auditorium_id_auditorium',
            ChoiceType::class,
            [
                'label' => 'label.auditoriums',
                'required' => true,
                'placeholder' =>'Wybierz salę...',
                'choices' => $this->prepareAuditoriumsForChoices($options['auditorium_repository']),
            ]
        );

        $builder->add(
            'movie_movie_id',
            ChoiceType::class,
            [
                'label' => 'label.movies',
                'required' => true,
                'placeholder' => 'Wybierz film...',
                'choices' => $this->prepareMoviesForChoices($options['movie_repository']),
            ]
        );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'auditorium-default', 'movie-default',
                'auditorium_repository' => null,
                'movie_repository' => null,
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'screening_type';
    }


    protected function prepareAuditoriumsForChoices($AuditoriumRepository)
    {

        $auditoriums = $AuditoriumRepository->findAll();
        $choices = [];

        foreach ($auditoriums as $auditorium) {
            $choices[$auditorium['name']] = $auditorium['auditorium_id'];
        }

        return $choices;
    }

    protected function prepareMoviesForChoices($MovieListRepository)
    {

        $movies = $MovieListRepository->findAll();
        $choices = [];

        foreach ($movies as $movie) {
            $choices[$movie['movie_title']] = $movie['movie_id'];
        }

        return $choices;
    }

}