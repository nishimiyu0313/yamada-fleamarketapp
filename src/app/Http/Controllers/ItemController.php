<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Payment;

use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function list()
    {
        $userId = auth()->id();

        $items = Item::where('user_id', '!=', $userId)
            ->orderBy('created_at', 'desc')->Paginate(8);
        return view('item.index', compact('items'));
    }

    public function mylist()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $favoriteItems = $user->likedItems()
            ->where('items.user_id', '!=', $user->id)
            ->orderBy('likes.created_at', 'desc')
            ->paginate(8);

        return view('item.mylist', compact('favoriteItems', 'user'));
    }

    public function detail($id)
    {
        $user = auth()->user();
        $profile = null;
        if ($user) {
            $profile = Profile::where('user_id', $user->id)->first();
        }

        $item = Item::with([
            'condition',
            'categories',
            'category',
            'comments' => function ($query) {
                $query->latest();
            },
            'comments.user.profile'
        ])->withCount('comments', 'likedUsers')->findOrFail($id);

        return view('item.detail', compact('item', 'user', 'profile'));
    }

    public function like(Item $item)
    {
        $user = Auth::user();
        $item->likedUsers()->syncWithoutDetaching([$user->id]);
        return redirect()->back();
    }

    public function unlike(Item $item)
    {
        $user = Auth::user();
        $item->likedUsers()->detach($user->id);

        return redirect()->back();
    }

    public function likedItems()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $items = $user->likedItems()
            ->orderBy('pivot_created_at', 'desc')
            ->get();

        return view('items.index', compact('items'));
    }

    public function commentStore(CommentRequest $request, Item $item)
    {
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->item_id = $item->id;
        $comment->save();

        return back();
    }
    public function index()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('item.sell', compact('conditions', 'categories'));
    }

    public function sell(ExhibitionRequest $request)
    {

        $imagePath = $request->image->store('images', 'public');


        $item = Item::create([
            'name' => $request->name,
            'condition_id' => $request->condition_id,
            'brand_name' => $request->brand_name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'user_id' => Auth::id()
        ]);


        $item->categories()->sync($request->category_ids ?? []);

        return redirect('/');
    }

    public function  profileSell(Request $request)
    {
        $user = auth()->user();

        $profile = Profile::firstOrNew(['user_id' => $user->id]);
       
        $listedItems  = Item::where('user_id', $user->id)->latest()->paginate(8);
        
        return view('item.profilesell', compact('user', 'listedItems', 'profile'));
    }

    public function  profileBuy(Request $request)
    {
        $user = auth()->user();
        $profile = Profile::where('user_id', $user->id)->first();
        $purchasedItems = Payment::where('user_id', $user->id)
            ->where('status', Payment::STATUS_COMPLETED)
            ->with('item')
            ->latest()
            ->paginate(8);

        return view('item.profilebuy', compact('user', 'profile', 'purchasedItems'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $type   = $request->input('type', '/');

        if ($type === '/') {
            $query = Item::orderBy('created_at', 'desc');
        } elseif ($type === 'mylist') {
            $query = Item::whereHas('likedUsers', function ($q) {
                $q->where('user_id', auth()->id());
            })->orderBy('created_at', 'desc');
        } else {
            abort(404);
        }
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }


        $items = $query->paginate(8)->appends($request->all());


        $favoriteItems = $type === 'mylist' ? $items : collect();


        $view = $type === '/' ? 'item.index' : 'item.mylist';

        return view($view, compact('items', 'keyword', 'favoriteItems'));
    }
}
