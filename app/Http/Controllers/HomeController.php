<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Display the shop page.
     */
    public function shop()
    {
        return view('shop');
    }

    /**
     * Display product details page.
     */
    public function product($slug)
    {
        return view('product', ['slug' => $slug]);
    }

    /**
     * Display category page.
     */
    public function category($slug)
    {
        return view('category', ['slug' => $slug]);
    }

    /**
     * Display cart page.
     */
    public function cart()
    {
        return view('cart');
    }

    /**
     * Display about page.
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Display contact page.
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Display terms page.
     */
    public function terms()
    {
        return view('terms');
    }

    /**
     * Display privacy page.
     */
    public function privacy()
    {
        return view('privacy');
    }

    /**
     * Display FAQ page.
     */
    public function faq()
    {
        return view('faq');
    }
}