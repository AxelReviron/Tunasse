<?php

namespace App\Http\Api\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;

interface CrudControllerContract
{
    /**
     * Model associated to this controller.
     *
     * @return class-string<Model>
     */
    public function model(): string;

    /**
     * API Resource associated to this controller.
     *
     * @return class-string<JsonResource>
     */
    public function resource(): string;

    /**
     * FormRequests associated to this controller.
     *
     * @return array<string, class-string<FormRequest>>
     */
    public function formRequests(): array;

    /**
     * Relations exposed through the API.
     *
     * @return string[]
     */
    public function relationships(): array;

    /**
     * Attributes searchable.
     *
     * @return string[]
     */
    public function searchable(): array;
}
