<div>
    <play
        :starting-game-state="@js(json_encode($state))"
        :player-id="{{ auth()->user()->id }}"
        :game-id="'{{ $game->id }}'"
    />
</div>
