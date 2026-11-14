<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\Site\SiteContent;

class ServiceController extends Controller
{
    public function index()
    {
        return view('site.services.index', ['services' => SiteContent::services()]);
    }

    public function show(string $slug)
    {
        $service = SiteContent::service($slug);

        abort_if($service === null, 404);

        $otherServices = collect(SiteContent::services())->except($slug)->all();

        return view('site.services.show', compact('service', 'otherServices'));
    }
}
