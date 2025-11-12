<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsersController extends Controller
{
    /**
     * Display user management page
     */
    public function index(Request $request)
    {
        $nameSearch = $request->input('name');
        $emailSearch = $request->input('email');
        $roleFilter = $request->input('role');

        // Show all users except super_admin
        $query = User::where('role', '!=', 'super_admin');

        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($nameSearch) {
            $query->where('name', 'like', "%{$nameSearch}%");
        }

        if ($emailSearch) {
            $query->where('email', 'like', "%{$emailSearch}%");
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => [
                'name' => $nameSearch,
                'email' => $emailSearch,
                'role' => $roleFilter,
            ],
        ]);
    }

    /**
     * Deactivate a user
     */
    public function deactivate(User $user)
    {
        // Prevent deactivating super_admin
        if ($user->isSuperAdmin()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Não é possível desativar um super admin.');
        }

        $user->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário desativado com sucesso!');
    }

    /**
     * Activate a user
     */
    public function activate(User $user)
    {
        $user->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuário ativado com sucesso!');
    }
}
