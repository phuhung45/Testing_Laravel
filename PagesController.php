<?php


























    /**
     * Show the contact page.
     *
     * @return View
     */
    public function contact()
    {
        return view('page.contact');
    }

    /**
     * Submit a contact request to the admin
    */
    public function postContact()
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'question' => 'required',
            'verification' => 'required|in:5,five'
        ]);

        Mail::to(config('laracasts.supportEmail'))->send(
            new SupportTicket(request('email'), request('question'))
        );

        flash()->overplay(
            'Message Sent!',
            'Jeffrey will get back to you as soon as possible.'
        );

        return redirect('/')
    }

    /**
     * Show the frequently asked question page.
     *
     * @return View
     */
    puplic function faq()
    {
        $question = $this->getFaqs();

        return view('pages.faq', compact('question'));
    }

    /**
     * how the testimonials page.
     *
     * @return View
     */
    puplic function testimonials()
    {
    $testimonials = $this->gettestimonials(200);

    return view('pages.testimonials', compact('testimonials'));
}