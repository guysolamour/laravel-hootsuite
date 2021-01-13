<?php

namespace Guysolamour\Hootsuite\Traits;

trait  HootsuiteTrait
{
    /**
     * Permet de ne desactiver hoostuite sur le modele
     *
     * @var boolean
     */
    protected  $publish_via_hootsuite = true;


    /**
     * How to schedule via hootsuite API
     *
     * @return void
     */
    abstract public function PublishToSocialNetworksViaHootsuite();

    /**
     * Get new Hootsuite instance
     *
     * @return Guysolamour\Hootsuite\Hootsuite;
     */
    public function hootsuite()
    {
        return hootsuite();
    }


    public static function bootHootsuiteTrait()
    {
        /**
         * @param \Illuminate\Database\Eloquent\Model $model
         */
        static::saved(function ($model) {
            if (
                method_exists($model, 'PublishToSocialNetworksViaHootsuite') &&
                $model->publish_via_hootsuite
            ) {
                $model->PublishToSocialNetworksViaHootsuite();
            }
        });
    }

}
