<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FiberReport;

class ReportController extends Controller
{
    public function index()
    {
        $reports = FiberReport::latest()->paginate(10);
        return view('fiber-reports.index', compact('reports'));
    }
}