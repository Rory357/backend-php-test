<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('user', $app['session']->get('user'));

    return $twig;
}));


$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', [
        'readme' => file_get_contents('README.md'),
    ]);
});


$app->match('/login', function (Request $request) use ($app) {
    $username = $request->get('username');
    $password = $request->get('password');

    if ($username) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $user = $app['db']->fetchAssoc($sql, array($username, $password));

        if ($user){
            $app['session']->set('user', $user);
            return $app->redirect('/todo');
        }
    }

    return $app['twig']->render('login.html', array());
});


$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    return $app->redirect('/');
});


$app->get('/todo/{id}', function ($id) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    if ($id){
        $sql = "SELECT * FROM todos WHERE id = ?";
        $todo = $app['db']->fetchAssoc($sql, array($id));

        return $app['twig']->render('todo.html', [
            'todo' => $todo,
        ]);
    } else {
        $sql = "SELECT * FROM todos WHERE user_id = ?";
        $todos = $app['db']->fetchAll($sql, array($user['id']));

        return $app['twig']->render('todos.html', [
            'todos' => $todos,
        ]);
    }
})
->value('id', null);


$app->post('/todo/add', function (Request $request) use ($app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $user_id = $user['id'];
    $description = $request->get('description');

    $sql = "INSERT INTO todos (user_id, description) VALUES ('$user_id', '$description')";
    $app['db']->executeUpdate($sql);

    $app['session']->getFlashBag()->add("success", "Task $description succesfully added");

    return $app->redirect('/todo');
});


$app->match('/todo/delete/id={id}', function ($id) use ($app) {

    $sql = "DELETE FROM todos WHERE id = '$id'";
    $app['db']->executeUpdate($sql);

    $app['session']->getFlashBag()->add("success", "Task succesfully deleted");

    $flashBag = $app['session']->getFlashBag();

    $response = json_encode([
        'success' => true,
        'messages' => $flashBag->get('success'),
        'id' => $id,
    ]);

    $flashBag->clear();

    return $response;

});

$app->match('/todo/complete/id={id}', function ($id) use ($app) {

    $sql = "UPDATE todos SET is_complete = 1 WHERE id = '$id'";
    $app['db']->executeUpdate($sql);

    $app['session']->getFlashBag()->add("success", "Task succesfully completed");

    $flashBag = $app['session']->getFlashBag();

    $response = json_encode([
        'success' => true,
        'messages' => $flashBag->get('success'),
        'id' => $id,
    ]);

    $flashBag->clear();

    return $response;
    
});

$app->match('/todo/view/id={id}', function ($id) use ($app) {

    $sql = "SELECT * FROM todos WHERE id = '$id'";
    $data = $app['db']->fetchAssoc($sql);

    return json_encode($data);

});