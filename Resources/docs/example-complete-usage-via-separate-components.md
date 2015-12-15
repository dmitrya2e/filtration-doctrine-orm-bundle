# Usage example via separate components

## Prerequisites of the example

- This example creates 2 filters (name and price)
- Template engine in this example is Twig
- Filtration form is created and passed to the template
- This example uses Doctrine ORM query builder as filtration handler, so you must enable it in bundle configuration:
```yaml
# app/config/config.yml

da2e_filtration:
    handlers:
        doctrine_orm: true
```

### Controller

Please, read carefully comments in the code. They are intended to explain what is happening in the example.

```php
// YourController.php

public function yourAction(Request $request)
{
    // Create a filtration handler (this example uses Doctrine ORM query builder).
    $queryBuilder = $this->get('doctrine.orm.entity_manager')
        ->getRepository('YourBundle:YourRepository')
        ->createQueryBuilder('foo');

    // You can set any query builder conditions if required, it will be kept along with the applied filters.
    $queryBuilder->where('foo.bar = :bar')->setParameter('bar', 'bar2');
    $queryBuilder->andWhere('foo.baz = :baz')->setParameter('baz', 'baz2');

    // 1. Get filter creator and create filters.
    $filterCreator = $this->get('da2e.filtration.filter.creator.filter_creator');

    // - 1st argument is filter alias from the service definition.
    // - 2nd argument is the name of the filter.
    // - 3rd argument contains filter options (optional argument).
    $filter1 = $filterCreator->create('da2e_orm_text_filter', 'name', ['field_name' => 'foo.name']);
    $filter2 = $filterCreator->create('da2e_orm_number_filter', 'price', ['field_name' => 'foo.price', 'float' => true]);

    // 2. Create a filter collection and add the filters.
    $collection = $this->get('da2e.filtration.filter.collection.creator.collection_creator')->create();
    $collection->addFilter($filter1);
    $collection->addFilter($filter2);

    // 3. Get form creator and create a form (this example creates a named form).
    // - 1st argument is the name of the root form.
    $form = $this->get('da2e.filtration.form.creator.form_creator')->createNamed('filters', $collection);
    $form->handleRequest($request);

    // 4. Get filter executor and execute filtration.
    $filterExecutor = $this->get('da2e.filtration.filter.executor.filter_executor');

    // You must pass the filtration handler in array as the 2nd argument.
    $filterExecutor->execute($collection, [$queryBuilder]);

    // You can also set query builder conditions after applying filtration.
    // However, do not use method setParameters([...]), because it will override everything set while applying filters.
    $queryBuilder->orderBy('foo.created', 'DESC');

    // Fetch filtered results in any way you need.
    $results = $queryBuilder->getQuery()->execute();

    // Finally, just return a standard response with form passed to the template.
    return $this->render('your/template.html.twig', [
        'form'    => $form->createView(),
        'results' => $results,
    ]);
}
```

### View

Filtration form is standard Symfony Form object, so you can pass a FormView ($form->createView()) to the template and use it as usual.

Template does not contain any special logic for rendering form - everything is done through standard Symfony/Twig functions.
Template engine in this example uses Twig, but since filtration form is a Symfony Form object, it is possible to use any preferable template engine.

```twig
# template.html.twig

<form action="..." method="GET">
    # Filtration form is standard Symfony form view object, so you could do anything you would do with forms in Twig.
    {{ form_row(form.filters.name) }}
    {{ form_row(form.filters.price) }}
    
    <input type="submit"/>
</form>
```
