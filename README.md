# Laravel-Model-Relation-Detector
This repository consists of two Files that can detect Eloquent models relationships as array of function names as keys and array as value contains relation type, related model and related model fillable.

You can use (Laravel Relation Detector) in two main ways.

## Method 1

You can access the detector class directly and use the two main methods of the detector:

```
    $detector = RelationDetector::detect(new Model())
    // You should pass a model instance to the detector
    1- $detector->hasRelations()
    // This method indicates whether the given model has relation ships
    2- $detector->getModelRelations()
    // This method returns an array of relations
```

Output of model relations can be as so


```
   array:1 [▼
    "comments" => array:3 [▼
      "relation" => "hasMany"
      "related" => "App\Comment"
      "fillable" => array:4 [▼
        0 => "recipe_id"
        1 => "author"
        2 => "rating"
        3 => "comment"
        ]
      ]
    ]
```

## Method 2


You can use the trait (HasRelationDetector) in any model you want to has a detector

Suppose you have a model called Recipe
```
    <?php

    namespace App;

    use App\Foundation\HasRelationDetector;
    use Illuminate\Database\Eloquent\Model;

    class Recipe extends Model
    {
        use HasRelationDetector;
        protected $fillable=[
            'thumbnail',
            'title',
            'rate',
            'description',
            'ingredients',
            'directions',
            'comments'
        ];

        public function comments()
        {
            return $this->hasMany(Comment::class, 'recipe_id');
        }

        public function anything()
        {
            return 'anything';
        }

    }
```

Then after using the traing you can call static action like so:

```
  $recipeRelationDetector = Recipe::getRelationDetector();
  // and then you can access the two functions of the detector directly.
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

This package is based on API called Battuta so thanks for the API creator.

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]
