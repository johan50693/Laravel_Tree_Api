<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

use App\Models\Node;
use App\Http\Resources\NodeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NodeController extends Controller
{
    #[OA\Post(
        path: "/api/nodes",
        operationId: "storeNode",
        summary: "Create new node",
        description: "Returns node data",
        tags: ["Nodes"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "parent", type: "integer", example: 1, nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Successful operation"),
            new OA\Response(response: 422, description: "Validation Error")
        ]
    )]

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent' => 'nullable|exists:nodes,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $node = Node::create([
            'parent_id' => $request->input('parent')
        ]);

        return new NodeResource($node);
    }

    #[OA\Get(
        path: "/api/nodes/parents",
        operationId: "getParents",
        summary: "List parent nodes",
        description: "Returns list of root nodes",
        tags: ["Nodes"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "Language code (ISO 639-1)",
                required: false,
                schema: new OA\Schema(type: "string", example: "en")
            ),
            new OA\Parameter(
                name: "timezone",
                in: "query",
                description: "Timezone for dates",
                required: false,
                schema: new OA\Schema(type: "string", example: "UTC")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]

    public function indexParents()
    {
        $roots = Node::whereNull('parent_id')->get();
        return NodeResource::collection($roots);
    }

    #[OA\Get(
        path: "/api/nodes/{id}/children",
        operationId: "getChildren",
        summary: "List children nodes",
        description: "Returns list of children nodes",
        tags: ["Nodes"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Node ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "depth",
                in: "query",
                description: "Depth of children",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "Language code (ISO 639-1)",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "timezone",
                in: "query",
                description: "Timezone",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation"),
            new OA\Response(response: 404, description: "Node not found")
        ]
    )]

    public function indexChildren($id, Request $request)
    {
        if (!Node::where('id', $id)->exists()) {
             return response()->json(['error' => 'Node not found'], 404);
        }
        
        $depth = (int) $request->input('depth', 1);
        if ($depth < 1) $depth = 1;
        
        $query = Node::where('parent_id', $id);
        
        if ($depth > 1) {
            $eagerLoad = [];
            $rel = 'children';
            for ($i = 1; $i < $depth; $i++) {
                $eagerLoad[] = $rel;
                $rel .= '.children';
            }
            $query->with($eagerLoad);
        }
        
        $children = $query->get();
        
        return NodeResource::collection($children);
    }

    #[OA\Delete(
        path: "/api/nodes/{id}",
        operationId: "deleteNode",
        summary: "Delete node",
        description: "Deletes a node if logic is met",
        tags: ["Nodes"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Node ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation"),
            new OA\Response(response: 400, description: "Node has children"),
            new OA\Response(response: 404, description: "Node not found")
        ]
    )]

    public function destroy($id)
    {
        $node = Node::find($id);
        if (!$node) {
            return response()->json(['error' => 'Node not found'], 404);
        }

        if ($node->children()->exists()) {
            return response()->json(['error' => 'Node has children and cannot be deleted.'], 400);
        }

        $node->delete();

        return response()->json(['message' => 'Node deleted successfully']);
    }
}
