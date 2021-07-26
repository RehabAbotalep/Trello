<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\WorkspaceType;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    use ApiResponse;

    public function workspaceTypes()
    {
        $types = WorkspaceType::get();
        return $this->apiResponse(200, 'Workspace types', null, $types);
    }

    public function addWorkspace(Request $request)
    {

    }
}
