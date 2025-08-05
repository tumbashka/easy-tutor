<?php

namespace App\Http\Controllers\Teacher;

use App\DTO\Board\BoardFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\Board\CopyBoardRequest;
use App\Http\Requests\Teacher\Board\SearchBoardRequest;
use App\Http\Requests\Teacher\Board\StoreBoardRequest;
use App\Http\Requests\Teacher\Board\UpdateBoardRequest;
use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(SearchBoardRequest $request)
    {
        $user = auth()->user();
        $subjects = $user->subjects;

        $boards = $user->boards()
            ->with('subject')
            ->filter(BoardFilterDTO::create($request->validated()))
            ->orderBy('updated_at', 'desc')
            ->paginate()
            ->withQueryString();


        return view('teacher.board.index', compact('subjects', 'boards'));
    }

    public function store(StoreBoardRequest $request)
    {
        $user = auth()->user();
        $user->boards()->create($request->validated());

        return back()->withSuccess('Доска успешно создана!');
    }

    public function update(Board $board, UpdateBoardRequest $request)
    {
        $board->update($request->validated());

        return back()->withSuccess('Изменение успешно сохранено!');
    }

    public function delete(Board $board)
    {
        $this->authorize('delete', $board);
        $board->delete();

        return back()->withSuccess('Доска успешно удалена!');
    }

    public function copy(Board $board, CopyBoardRequest $request)
    {
        $newBoard = $board->replicate()->fill($request->validated());
        $newBoard->save();

        return back()->withSuccess('Копирование успешно!');
    }
}
