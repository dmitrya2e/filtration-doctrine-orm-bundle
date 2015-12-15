# Usage example via FilterSuperManager

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

    // 1. Get filter super manager via service container.
    $manager = $this->get('da2e.filtration.manager.filter_super_manager');
    
    // 2. Create a filter collection.
    $collection = $manager->createFilterCollection();

    // 3. Add filters to the collection.
    // - 2nd argument is filter alias from the service definition.
    // - 3rd argument is the name of the filter.
    // - 4th argument contains filter options (optional argument).
    $manager->addFilter($collection, 'da2e_orm_text_filter', 'name', [
        'field_name' => 'foo.db_name',
    ]);
    
    $manager->addFilter($collection, 'da2e_orm_number_filter', 'price', [
        'field_name'    => 'foo.price', 
        'default_value' => 100
    ]);

    // 4. Create filtration form.
    // - 1st argument is the root form name.
    // - Empty arrays in arguments are form options (there are no form options in this example).
    // - Passed $request argument will guarantee that the form will handle the request ($form->handleRequest($request)).
    $form = $manager->createNamedForm('filters', $collection, [], [], $request);
    
    // 5. Execute filtration.
    // You must pass the filtration handler in array as the 2nd argument.    
    $manager->executeFilters($collection, [$queryBuilder]);
    
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
