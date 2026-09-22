<?php
namespace kilian\chatadmin\services;

final class RemovalPolicy
{
    public static function allows(bool $isAdmin, bool $actorIsParticipant, int $actorId, int $targetId, bool $targetIsParticipant, int $participantCount): bool
    {
        return $isAdmin && $actorIsParticipant && $targetIsParticipant
            && $actorId > 0 && $targetId > 0 && $actorId !== $targetId
            && $participantCount > 1;
    }
}
