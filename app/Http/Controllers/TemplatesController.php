<?php

namespace App\Http\Controllers;

use App\Stop;
use App\Template;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    public function createTemplate(Request $request)
    {
        try {
            $template = new Template();
            $template->from = $request->input('from');
            $template->to = $request->input('to');
            $template->save();
            foreach ($request->stopIds as $index=>$stopId)
            {
                $template->stops()->attach([$stopId => ['order' => $index + 1]]);
            }
        } catch (Exception $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([$e->errorInfo[2]], 409);
            }
            return response()->json([$e->errorInfo[2]], 500);
        }
        return response()->json($template);
    }

    public function getTemplates()
    {
        $templates = Template::with('stops')->get();
        foreach ($templates as $r) {
            $r->from = Stop::findOrFail($r->from);
            $r->to = Stop::findOrFail($r->to);
        }
        return response()->json($templates);
    }

    public function getTemplate($id)
    {
        $template = Template::with('stops')->get()->find($id);
        $template->from = Stop::findOrFail($template->from);
        $template->to = Stop::findOrFail($template->to);
        return response()->json($template);
    }

    public function editTemplate(Request $request, $id)
    {
        $template = Template::findOrFail($id);
        $template->update($request->all());
        $template->stops()->detach();
        foreach ($request->stopIds as $index=>$stopId)
        {
            $template->stops()->attach([$stopId => ['order' => $index + 1]]);
        }

        return $template;
    }

    public function deleteTemplate(Request $request, $id)
    {
        $res = Template::where('id', $id)->delete();
        return $res;
    }

}
