<?php

use Symfony\Component\HttpFoundation\Request;

use model\Dao\Dao;
use model\Entite\Film;
use forms\FilmForm;
use forms\FilmRechercheForm;

$filmsControllers = $app['controllers_factory'];

$filmsControllers->get('/', function () use ($app) {
    $filmDao = Dao::getInstance()->getFilmDAO();
    try {
        $films = $filmDao->findAll();
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
    }
    

    $form = $app['form.factory']->create(new FilmRechercheForm());

    return $app['twig']->render('pages/intranet/films/list.html.twig', array(
        'entities' => $films,
        'form' => $form->createView()
    ));
})->bind('intranet-films-list');

$filmsControllers->post('/search', function (Request $request) use ($app) {
    $films = array();
    $form = $app['form.factory']->create(new FilmRechercheForm());
    
    $form->handleRequest($request);
    
    if ($form->isValid()) {
        try {
            $filmDao = Dao::getInstance()->getFilmDAO();
            $data = $form->getData();
            $films = $filmDao->findAllByTitle($data['titre']);
        } catch (Exception $e) {
            $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
        }
        
    }

    return $app['twig']->render('pages/intranet/films/list.html.twig', array(
        'entities' => $films,
        'form' => $form->createView()
    ));
})->bind('intranet-films-search');

$filmsControllers->get('/new', function () use ($app) {
    try {
        $genreDao = Dao::getInstance()->getGenreDAO();
        $genres = $genreDao->findAll();

        $distributeurDao = Dao::getInstance()->getDistributeurDAO();
        $distributeurs = $distributeurDao->findAll();
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
    }
    

    $form = $app['form.factory']->create(new FilmForm($genres, $distributeurs));

    return $app['twig']->render('pages/intranet/films/new.html.twig', array(
        'form' => $form->createView()
    ));
})->bind('intranet-films-new');

$filmsControllers->post('/create', function (Request $request) use ($app) {
    $genreDao = Dao::getInstance()->getGenreDAO();
    try {
        $genres = $genreDao->findAll();
        
        $distributeurDao = Dao::getInstance()->getDistributeurDAO();
        $distributeurs = $distributeurDao->findAll();
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
    }
    

    $form = $app['form.factory']->create(new FilmForm($genres, $distributeurs));

    if ($request->getMethod() === 'POST') {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            try {
                $genre = $genreDao->find($data['genre']);
                $distributeur = $distributeurDao->find((int) $data['distributeur']);
            } catch (Exception $e) {
                $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
            }
            

            $film = new Film();
            $film->setTitre($data['titre']);
            $film->setDateDeSortie($data['dateDeSortie']);
            $film->setAgeMinimum($data['ageMinimum']);
            $film->setGenre($genre);
            $film->setDistributeur($distributeur);

            $filmDao = Dao::getInstance()->getFilmDao();
            try {
                if ($filmDao->create($film)) {
                    $app['session']->getFlashBag()->add('success', 'Le film est bien enregistré !');
                
                    return $app->redirect($app['url_generator']->generate('intranet-films-list'));
                } else {
                    $app['session']->getFlashBag()->add('error', 'Le film n\'a pas pu être enregistré.');
                }
            } catch (Exception $e) {
                $app['session']->getFlashBag()->add('error', 'Le film n\'a pas pu être enregistré.');
            }
            
        }
    }

    return $app['twig']->render('pages/intranet/films/new.html.twig', array(
        'form' => $form->createView()
    ));
})->bind('intranet-films-create');

$filmsControllers->get('/{id}', function ($id) use ($app) {
    $filmDao = Dao::getInstance()->getFilmDAO();
    $film = $filmDao->find($id);

    if (!$film) {
        $app->abort(404, 'Ce film n\'existe pas...');
    }

    $deleteForm = $app['form.factory']->createBuilder('form', array('id' => $id))
        ->add('id', 'hidden')
        ->getForm();

    return $app['twig']->render('pages/intranet/films/detail.html.twig', array(
        'entity' => $film,
        'delete_form' => $deleteForm->createView()
    ));
})->bind('intranet-films-detail');

$filmsControllers->get('/{id}/edit', function ($id) use ($app) {
    try {
        $filmDao = Dao::getInstance()->getFilmDao();
        $film = $filmDao->find($id);
    } catch (Exception $e) {
        $app->abort(404, 'Ce film n\'existe pas...');
    }
    if (!$film) {
        $app->abort(404, 'Ce film n\'existe pas...');
    }
    try {
        $genreDao = Dao::getInstance()->getGenreDAO();
        $genres = $genreDao->findAll();
        
        $distributeurDao = Dao::getInstance()->getDistributeurDAO();
        $distributeurs = $distributeurDao->findAll();
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
    }

    $dateDeSortie = $film->getDateDeSortie();

    $form = $app['form.factory']->create(new FilmForm($genres, $distributeurs), array(
        'titre' => $film->getTitre(),
        'dateDeSortie' => $dateDeSortie,
        'ageMinimum' => $film->getAgeMinimum(),
        'genre' => $film->getGenre()->getNom(),
        'distributeur' => $film->getDistributeur()->getId(),
    ));

    $deleteForm = $app['form.factory']->createBuilder('form', array('id' => $id))
        ->add('id', 'hidden')
        ->getForm();

    return $app['twig']->render('pages/intranet/films/edit.html.twig', array(
        'entity' => $film,
        'form' => $form->createView(),
        'delete_form' => $deleteForm->createView()
    ));
})->bind('intranet-films-edit');

$filmsControllers->post('/{id}/update', function (Request $request, $id) use ($app) {
    try {
        $filmDao = Dao::getInstance()->getFilmDao();
        $film = $filmDao->find($id);
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
    }
    

    if (!$film) {
        $app->abort(404, 'Ce film n\'existe pas...');
    }
    try {
        $genreDao = Dao::getInstance()->getGenreDAO();
        $genres = $genreDao->findAll();
        $distributeurDao = Dao::getInstance()->getDistributeurDAO();
        $distributeurs = $distributeurDao->findAll();
        $form = $app['form.factory']->create(new FilmForm($genres, $distributeurs), array(
            'titre' => $film->getTitre(),
            'dateDeSortie' => $film->getDateDeSortie(),
            'ageMinimum' => $film->getAgeMinimum(),
            'genre' => $film->getGenre()->getNom(),
            'distributeur' => $film->getDistributeur()->getId(),
        ));
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Vous avez généré une excection SQL, réassayez.');
    }
    

    if ($request->getMethod() === 'POST') {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            try {
                $genre = $genreDao->find($data['genre']);
                $distributeur = $distributeurDao->find((int) $data['distributeur']);
                
                $film->setTitre($data['titre']);
                $film->setDateDeSortie($data['dateDeSortie']);
                $film->setAgeMinimum($data['ageMinimum']);
                $film->setGenre($genre);
                $film->setDistributeur($distributeur);
                
                if ($filmDao->update($film)) {
                    $app['session']->getFlashBag()->add('success', 'Le film est bien mis à jour !');
                
                    return $app->redirect($app['url_generator']->generate('intranet-films-list'));
                } else {
                    $app['session']->getFlashBag()->add('error', 'Le film n\'a pas pu être mis à jour.');
                }
            } catch (Exception $e) {
                $app['session']->getFlashBag()->add('error', 'Le film n\'a pas pu être mis à jour.');
            }
            
        }
    }

    $deleteForm = $app['form.factory']->createBuilder('form', array('id' => $id))
        ->add('id', 'hidden')
        ->getForm();

    return $app['twig']->render('pages/intranet/films/edit.html.twig', array(
        'entity' => $film,
        'form' => $form->createView(),
        'delete_form' => $deleteForm->createView()
    ));
})->bind('intranet-films-update');

$filmsControllers->post('/{id}/delete', function (Request $request, $id) use ($app) {
    try {
        $filmDao = Dao::getInstance()->getFilmDao();
        $film = $filmDao->find($id);
    } catch (Exception $e) {
        $app->abort(404, 'Ce film n\'existe pas...');
    }

    if (!$film) {
        $app->abort(404, 'Ce film n\'existe pas...');
    }
    try {
        if ($filmDao->delete($film)) {
            $app['session']->getFlashBag()->add('success', 'Le film a bien été supprimé !');
        } else {
            $app['session']->getFlashBag()->add('error', 'Le film n\'a pas pu être supprimé...');
        }
    } catch (Exception $e) {
        $app['session']->getFlashBag()->add('error', 'Le film n\'a pas pu être supprimé...');
    }
    

    return $app->redirect($app['url_generator']->generate('intranet-films-list'));
})->bind('intranet-films-delete');

$app->mount('/intranet/films', $filmsControllers);
