# Brief overview of all components and workflow

## Introduction

Whole workflow of the filtration bundle consist of several components:
- Abstract filter object
- Filter creator
- Filter option handler
- Filter collection creator
- Filter collection
- Filter executor
- Form creator

However, it is not necessary to use exactly these components (which would be strange, because the bundle provides them ready and for specific purposes).

You can replace any of them with your own implementations or you can use only concrete filter objects (which are based on abstract filter object) without any of other components, or even use FilterInterface only to build your own custom filter.

## Components overview

### Abstract filter

Abstract filter is a base filter class, which is extended by all standard filters packaged within FiltrationBundle.
This class provides maximum capabilities FiltrationBundle can offer:
- Option setting via filter option handler component (implemented FilterOptionInterface)
- Form handling (implemented FilterHasFormInterface)
- ... and much more. Learn more about abstract filter [here](filters-reference-abstract-filter.md).

Core abstract methods, which are implemented in every filter, are:
- **applyFilter** - applying filter on filtration handler
- **convertValue** - converts raw value (e.g. set by a form) into a "workable" and clean format

If you build your own filter, you can easily extend this class and it will give you maximum capabilities listed above.

However, it is not required to extend AbstractFilter for your own filters, if you don't need all of these capabilities. 

Keep in mind, that:
- The only essential requirement to build a filter is to have it implemented **FilterInterface**.
- If you want your filter to be able to have an option setting (array('foo' => 'bar') instead of $filter->setFoo('bar')), the filter must implement **FilterOptionInterface**.
- If you want your filter to be able to handle form,  the filter must implement **FilterHasFormInterface**.

Learn more about filters [here](filters-reference.md).

### Filter creator

Filter creator is responsible for creating filters. Example:

```php
$creator = $serviceContainer->get('da2e.filtration.filter.creator.filter_creator');
$filter = $creator->create('da2e_orm_text_filter', 'filter_name', ['filter', 'options']);
```

### Filter option handler

### Filter collection creator

### Filter collection

### Filter executor

### Form creator

## Filtration form

As it was already mentioned, the bundles core feature is a form support.

FiltrationBundle uses standard Symfony form objects to provide this possibility. The form is being mapped with fields and populated from the filter collection.

The form can be created via form creator component and passed to the template as FormView object.

[Learn more about form creator component](form-creator.md).

### Filter form type

To create a form field for each filter, it is required to have a prototype of form type object.

The bundle is packaged with a default one filter form type - **FilterType**, which is used for every filter. 
It is just an instance of Symfony FormTypeInterface with minimal configuration in it.

If for some reason you need your own form type object, you can easily override it in configuration.

[Learn more about form creator component](form-filter-type.md).

### Filters in templates

Since FiltrationBundle uses standard Symfony form objects and forms are being passed as FormView objects to templates, it is possible to do anything you would do with any other forms in templates.
This also means that you can use any template engine, which Symfony supports.

## How to glue everything together

The bundle is packaged with a single manager, which is responsible for all workflow and all components.
The manager is named as **FilterSuperManager**. It is not really proper way to handle things like this, with one big manager, however sometimes it may be convenient to work with single tool rather than with many separate components.
Basically, FilterSuperManager is just a wrapper with all necessary components inside.

I would suggest to use FilterSuperManager if your filtration requirements does not lead to complicated infrastructure in your code (e.g. handle all filtration workflow in an action of a controller).
In opposite, if you have a complex layer of filtration architecture, I assume it would be more correctly to use separate filtration components (via DI), which would also give an additional control over whole workflow.

Anyway, you are free to choose whether to use FilterSuperManager or separate components.

Check out the examples of usage through different ways:
- [Complete usage example via FilterSuperManager](example-complete-usage-via-filtersupermanager.md)
- [Complete usage example via separate components](example-complete-usage-via-separate-components.md)

## Components customization

Each of the components is fully customizable, which means you can override any of them just by redefining a specific parameters in parameters file (app/parameters.yml) or by configuring them in bundle configuration (app/config.yml). 

For example, to override filter collection component, you can just redefine the following parameter:
```yaml
# app/parameters.yml

parameters:
    da2e.filtration.filter.collection.collection_class: Your\Collection\Class
```

For more details on configuration please refer to:
- [Configuration reference](config-reference-config.md)
- [Configuration reference via container parameters](config-reference-parameters.md)
- [Example of customization and override of standard bundle components](example-customize-standard-components.md)
