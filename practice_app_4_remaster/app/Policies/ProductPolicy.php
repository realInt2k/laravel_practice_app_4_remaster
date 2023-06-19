<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Response as HttpResponse;

class ProductPolicy
{
    private function userHasProduct(User $user, Product $product): Response
    {
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            if (!$user->hasProduct($product->id)) {
                return Response::denyWithStatus(HttpResponse::HTTP_FORBIDDEN, 'You do not own this product.');
            }
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): Response
    {
        return $this->userHasProduct($user, $product);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): Response
    {
        return $this->userHasProduct($user, $product);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): Response
    {
        return $this->userHasProduct($user, $product);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return true;
    }
}
