<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Credential;

trait ManagesCredentials
{
    /**
     * Get the collection of recipes.
     *
     * @return \Laravel\Forge\Resources\Credential[]
     */
    public function credentials()
    {
        return $this->transformCollection(
            $this->get('credentials')['credentials'], Credential::class
        );
    }
}
