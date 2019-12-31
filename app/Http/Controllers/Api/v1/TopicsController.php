<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests\Api\TopicRequest;
use App\Models\Topic;
use App\Http\Resources\TopicResource;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\User;

class TopicsController extends Controller
{
    public function index(Request $request, Topic $topic)
    {
        /*$topics = QueryBuilder::for(Topic::class)->get();
            ->allowedIncludes('user', 'category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ])
            ->paginate();*/

        $query = $topic->query();

        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }

        $topics = $query
            ->with('user', 'category')
            ->withOrder($request->order)
            ->paginate();

        return TopicResource::collection($topics);
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();

        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->all());
        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return response(null, 204);
    }

    public function userIndex(Request $request, User $user)
    {
       /* $query = $user->topics()->getQuery();

        $topics = QueryBuilder::for($query)
            ->allowedIncludes('user', 'category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ])
            ->paginate();*/

        $query = $user->topics()->getQuery();

        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }

        $topics = $query
            ->with('user', 'category')
            ->withOrder($request->order)
            ->paginate();

        return TopicResource::collection($topics);
    }

    public function show(Topic $topic)
    {
        /*$topic = QueryBuilder::for(Topic::class)
            ->allowedIncludes('user', 'category')
            ->findOrFail($topicId);
        return new TopicResource($topic);
        */

        return new TopicResource($topic->load('user', 'category'));
    }
}
