<?php

namespace App\Http\Controllers\API\Explorer;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Http\Resources\Explorer\Portfolio as PortfolioResource;
use App\Models\PortfolioMedia;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PortfolioRestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Portfolio[]|Collection|\Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Portfolio::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $params = $request->all();

        $portfolio = new Portfolio();
        $portfolio->name = $params['portfolio_name'];
        $portfolio->description = $params['portfolio_description'];
        $portfolio->video_url = $params['portfolio_video_url'];
        $portfolio->save();


        $user = \Auth::user();
        $portfolio->users()->attach($user);

        foreach ($params['portfolio_medias'] as $portfolio_media) {
            $media = new PortfolioMedia();


            $filename = time() . '_' . $portfolio_media->getClientOriginalName();
            $media->name = $filename;

            // File extension
            $media->extension = $portfolio_media->getClientOriginalExtension();

            // File upload location
            $location = 'upload/portfolio-media';

            // Upload file
            $portfolio_media->move($location, $filename);

            // File path
            $media->path = '/' . $location . '/' . $filename;

            $media->media_type = 'image';
            $media->portfolio_id = $portfolio->id;

            $media->save();
        }

        return $portfolio;
    }

    /**
     * Display the specified resource.
     *
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        //
        return new PortfolioResource($portfolio);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        //
        return $portfolio->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Portfolio $portfolio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portfolio $portfolio)
    {
        //
        return $portfolio->delete();
    }
}
