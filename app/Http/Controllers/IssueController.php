<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueRequest;
use App\Model\Issue;
use App\Model\IssueImage;
use function GuzzleHttp\Promise\all;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $issues = Issue::with('issueImages')->get();
        return response()->json($issues);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueRequest $request)
    {
        $image_data = [];
        $issue = new Issue([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'user_id' => Auth::user()->id,

            ]);
        $issue->save();

        if ($request->hasFile('images'))
        {
            $images = $request->file('images');
            foreach ($images as $image)
            {
                $image_data[] = Storage::disk('public')->put('issues', $image);
            }
        }
        foreach ($image_data as $image)
        {
            $ticket_image = new IssueImage([
                'issue_id' => $issue['id'],
                'image' => $image
            ]);
            $ticket_image->save();
        }

        return response()->json([
            'message' => "Issue Created Successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $issue = Issue::with('issueImages')->findOrFail($id);
        return response()->json([
           $issue
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function edit(Issue $issue)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Issue  $issue
     * @return \Illuminate\Http\Response
     */

    public function update( IssueRequest $request, $id)
    {
        $issue = Issue::findOrFail($id);
        $issue->title = $request->get('title');
        $issue->description = $request->get('description');
        $issue->save();
        if ($request->hasFile('images'))
        {
            $new_images = $request->file('images');
            $images = IssueImage::where('issue_id' , $id)->get();
            if (count($images))
            {
                foreach ( $images as $image)
                {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();

                }
            }
            foreach ($new_images as $image)
            {
               $image_path = Storage::disk('public')->put('issues', $image);
                $ticket_image = new IssueImage([
                    'issue_id' => $issue['id'],
                    'image' => $image_path
                ]);
                $ticket_image->save();
            }
        }
        return response()->json([
            'message' => "Issue Updated Successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if( Issue::findOrFail($id)->delete() ) {
            $issue_image = IssueImage::where('issue_id' , $id)->get();
            foreach ($issue_image as $image)
            {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }
            return response()->json([
                'message' => "Issue Deleted Successfully"
            ]);
        }
        return response()->json([
            'message' => "Unable to Delete Issue!"
        ]);
    }
}
