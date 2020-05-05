<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadFilters;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class TrendingThreadsTest
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);

    }

    /**
     *  Display a listing of the resource.
     *
     * @param Chanel        $channel
     * @parma ThreadFilters $filters
     * @return \Illuminate\Http\Réponce
    */
    public function index(Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters, Trending $trending);

        if (request()->wantsJson()) {
            return $threads;
        }

        $trending = $trending->get();
        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending
        ]);
    }

    /**
     * Show the form for creating a new réource
     *
     * @returnn \Illuminate\Http\Responce
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @returnn \Illuminate\Http\Responce
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channel,id'
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id,
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body'=> request('body')
        ]);

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
    }

    /**
     * Display the specified resource.
     *
     * @param integer $channel
     * @param App\Thread $Thread
     * @returnn \Illuminate\Http\Responce
     */
    public function show($channel, Thread $Thread, Trending $thread)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

//

        return view('threads.show', compact('thread'));
    }
}