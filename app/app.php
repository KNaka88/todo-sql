<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";

    $app = new Silex\Application();

    $server = 'mysql:host=localhost:8889;dbname=to_do';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);
     $app['debug'] = true;

     use Symfony\Component\HttpFoundation\Request;
     Request::enableHttpMethodParameterOverride();

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    $app->get("/", function() use ($app) {

        return $app['twig']->render('tasks.html.twig', array('categories' => Category::getAll()));
    });

    $app->post("/categories", function() use ($app) {
        $category = new Category($_POST['name']);
        $category->save();
        return $app['twig']->render('tasks.html.twig', array('categories'=>Category::getAll(), 'tasks'=>Task::getAll()));
    });

    $app->get("/categories/{id}", function($id) use ($app){
      $category = Category::find($id);
      return $app ['twig']->render('category.html.twig', array('category'=> $category, 'tasks'=>$category->getTasks()));
    });

    $app->post("/tasks", function() use ($app) {
        $description = $_POST['description'];
        $category_id = $_POST['category_id'];
        $task = new Task($description, $id = null, $category_id);
        $task->save();
        $category = Category::find($category_id);
        // $task->sortTask();
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks'=>$category->getTasks()));
    });

    $app->post("/delete_tasks", function() use ($app) {
        Task::deleteAll();
        Category::deleteAll();
        return $app['twig']->render('delete_tasks.html.twig');
    });

    $app->get("/categories/{id}/edit", function($id) use ($app){
        $category = Category::find($id);
        return $app['twig']->render('category_edit.html.twig', array('category'=>$category));
    });

    $app->patch("/categories/{id}", function($id) use ($app){
        $name =$_POST['name'];
        $category = Category::find($id);
        $category->update($name);
        return $app['twig']->render('category.html.twig',array('category'=>$category, 'tasks'=> $category->getTasks()));
    });

    $app->delete("/categories/{id}", function($id) use ($app){
        $category = Category::find($id);
        $category->delete();
        return $app['twig']->render("tasks.html.twig", array('categories' => Category::getAll()));
    });


    return $app;
?>
