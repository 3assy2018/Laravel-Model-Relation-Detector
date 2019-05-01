<?php
namespace App\Foundation;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

class RelationDetector
{
    private $model;
    /**
     * RelationDetector constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model= $model;
    }

    /**
     * Parse Model Reflection
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    private function getModelReflection()
    {
        return (new ReflectionClass($this->model));
    }

    /**
     * Parse Model Reflection Methods
     * @return ReflectionMethod[]
     * @throws \ReflectionException
     */
    private function getModelMethods()
    {
        return $this->getModelReflection()->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    /**
     * Validate That a Given Reflection Method Is High Level Module Method
     * @param ReflectionMethod $reflectionMethod
     * @return bool
     * @throws \ReflectionException
     */
    private function validateHighLevelModuleMethod(ReflectionMethod $reflectionMethod)
    {
        return $reflectionMethod->class == $this->getModelReflection()->name;
    }

    /**
     * Validate That a Given Reflection Method Does Not Have Parameters According To Laravel Relation Methods Convention
     * @param ReflectionMethod $reflectionMethod
     * @return bool
     */
    private function validateEmptyParameters(ReflectionMethod $reflectionMethod)
    {
        return empty($reflectionMethod->getParameters());
    }

    /**
     * Validate That a Given Reflection Method Applies Some Criteria
     * @param ReflectionMethod $reflectionMethod
     * @return bool
     * @throws \ReflectionException
     */
    private function validateModelMethods(ReflectionMethod $reflectionMethod)
    {
        return $this->validateHighLevelModuleMethod($reflectionMethod) && $this->validateEmptyParameters($reflectionMethod);
    }

    /**
     * Invoke Given Reflection Method By Injected Model
     * @param ReflectionMethod $reflectionMethod
     * @return mixed
     */
    private function reflectionMethodInvoke(ReflectionMethod $reflectionMethod)
    {
        return $reflectionMethod->invoke($this->model);
    }

    /**
     * Return a Collection Of Relations
     * @return array
     * @throws \ReflectionException
     */
    public function getModelRelations()
    {
        $relations= [];
        foreach ($this->getModelMethods() as $modelMethod){
            if($this->validateModelMethods($modelMethod)){
                $invoker = $this->reflectionMethodInvoke($modelMethod);
                if($invoker instanceof Relation){
                    $relations[$modelMethod->name]['relation']=
                        camel_case((new ReflectionClass($invoker))->getShortName());
                    $relations[$modelMethod->name]['related']=
                        (new ReflectionClass($invoker->getRelated()))->name;
                    $relations[$modelMethod->name]['fillable']=
                        $invoker->getRelated()->getFillable();
                }
            }
        }
        return $relations;
    }

    /**
     * Indicates Whether Model Has Relations Or Not
     * @return bool
     * @throws \ReflectionException
     */
    public function hasRelations()
    {
        return !empty($this->getModelRelations());
    }

    /**
     * Clean static handler
     * @param Model $model
     * @return RelationDetector
     */
    public static function detect(Model $model)
    {
        return  (new static($model));
    }
}