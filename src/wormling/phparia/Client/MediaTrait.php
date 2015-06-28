<?php

/*
 * Copyright 2014 Brian Smith <wormling@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace phparia\Client;

use phparia\Exception\ConflictException;
use phparia\Exception\NotFoundException;
use phparia\Resources\Playback;

/**
 * Media playback helper methods
 *
 * requires playMedia method and client
 *
 * @author Brian Smith <wormling@gmail.com>
 */
trait MediaTrait
{
    /**
     * @var AriClient
     */
    public abstract function getClient();

    /**
     * Start playback of media. The media URI may be any of a number of URI's. Currently sound:,
     * recording:, number:, digits:, characters:, and tone: URI's are supported. This operation creates a
     * playback resource that can be used to control the playback of media (pause, rewind, fast forward,
     * etc.)
     *
     * @link https://wiki.asterisk.org/wiki/display/AST/ARI+and+Channels%3A+Simple+Media+Manipulation Simple media playback
     *
     * @param string $channelId Channel's id
     * @param string $media (required) Media's URI to play.
     * @param string $lang For sounds, selects language for sound.
     * @param int $offsetms Number of media to skip before playing.
     * @param int $skipms (3000 default) Number of milliseconds to skip for forward/reverse operations.
     * @param string $playbackId Playback Id.
     * @return Playback
     * @throws NotFoundException
     * @throws ConflictException
     */
    public abstract function playMedia(
        $channelId,
        $media,
        $lang = null,
        $offsetms = null,
        $skipms = null,
        $playbackId = null
    );

    /**
     * Say a one or more digits.
     *
     * @param string $channelId
     * @param string $digits
     * @param string $playbackId
     *
     * @return \phparia\Resources\Playback
     */
    public function sayDigits($channelId, $digits, $playbackId = null)
    {
        $playback = $this->playMedia($channelId, "digits:$digits", null, null, null, $playbackId);

        return $playback;
    }

    /**
     * Say a number.
     *
     * @param $channelId
     * @param integer $number
     * @param string $playbackId
     *
     * @return \phparia\Resources\Playback
     */
    public function sayNumber($channelId, $number, $playbackId = null)
    {
        $playback = $this->playMedia($channelId, "number:$number", null, null, null, $playbackId);

        return $playback;
    }

    /**
     * Say a date/time.
     *
     * @todo Some combinations may need some silence playback to sound correct
     * @param $channelId
     * @param \DateTime $timestamp
     * @param string $format
     * @param string $playbackId
     * @link http://www.voip-info.org/wiki/view/Asterisk+cmd+SayUnixTime
     *
     * @return PlaybackList
     */
    public function sayDateTime($channelId, $timestamp, $format, $playbackId = null)
    {
        $format = str_replace('R', 'HM', $format); // 24 Hour, Minute - 24 hour time, including minute (HM)
        $format = str_replace('T', 'HMS',
            $format); // 24 Hour, Minute, Second - 24 hour clock with minute and second (HMS)

        $playbackList = new PlaybackList($this->getClient());

        $parts = str_split($format);
        $i = 0;
        foreach ($parts as $part) {
            switch ($part) {
                case 'A': // Day of week - Saturday, Sunday, ..., Friday
                case 'a':
                    $sound = 'digits/day-'.$timestamp->format('w');
                    $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'B': // Month name - January, February, ..., December
                case 'b':
                case 'h':
                    $sound = 'digits/mon-'.($timestamp->format('n') - 1);
                    $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'd': // numeric day of month - first, second, ..., thirty-first
                case 'e':
                    $dayOfMonth = $timestamp->format('j');
                    if ($dayOfMonth < 21 || $dayOfMonth === 30) {
                        $sound = 'digits/h-'.$timestamp->format('j');
                        $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null,
                            "$playbackId-$i");
                    } else {
                        $sound = 'digits/'.((int)($dayOfMonth / 10)) * 10;
                        $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null,
                            "$playbackId-$i");
                        $sound = 'digits/h-'.str_split($dayOfMonth)[1];
                        $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null,
                            "$playbackId-$i");
                    }
                    break;
                case 'Y': // Year
                    $sound = $timestamp->format('Y');
                    $playbackList[] = $this->playMedia($channelId, "number:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'I': // Hour, 12 hour clock - one, two, three, ..., twelve
                case 'l':
                    $sound = $timestamp->format('g');
                    $playbackList[] = $this->playMedia($channelId, "number:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'H': // Hour, 24 hour clock - ?, oh one, oh two, ..., oh nine, ten, eleven, ..., twenty-three
                    $sound = $timestamp->format('H');
                    $playbackList[] = $this->playMedia($channelId, "number:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'k': // Hour, 24 hour clock - ?, one, two, three, ..., twenty three
                    $sound = $timestamp->format('G');
                    $playbackList[] = $this->playMedia($channelId, "number:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'M': // Minute - ?, oh one, oh two, ... fifty-nine
                    $sound = $timestamp->format('i');
                    $playbackList[] = $this->playMedia($channelId, "number:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'm': // Month number - Say number of month (first - twelfth)
                    $sound = 'digits/h-'.$timestamp->format('n');
                    $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'P': // AM or PM - ay em / pee em
                case 'p':
                    if ($timestamp->format('a') === 'am') {
                        $sound = 'digits/a-m';
                    } else {
                        $sound = 'digits/p-m';
                    }
                    $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null, "$playbackId-$i");
                    break;
                case 'Q': // Date - "today", "yesterday" or ABdY
                    if ($timestamp->format('d/m/Y') === date('d/m/Y')) { // Today
                        $sound = 'digits/today';
                        $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null,
                            "$playbackId-$i");
                    } else {
                        if ($timestamp === date('d/m/Y', time() - (24 * 60 * 60))) { // Yesterday
                            $sound = 'digits/yesterday';
                            $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null,
                                "$playbackId-$i");
                        } else {
                            $this->sayDateTime($channelId, $timestamp, 'ABdY', "$playbackId-$i");
                        }
                    }
                    break;
                case 'q': // Date - "" (for today), "yesterday", weekday, or ABdY
                    if ($timestamp->format('d/m/Y') !== date('d/m/Y')) { // Today
                        if ($timestamp === date('d/m/Y', time() - (24 * 60 * 60))) { // Yesterday
                            $sound = 'digits/yesterday';
                            $playbackList[] = $this->playMedia($channelId, "sound:$sound", null, null, null,
                                "$playbackId-$i");
                        } elseif ($timestamp->getTimestamp() < 604800) { // Within a week
                            $playbackList->exchangeArray(array_merge($playbackList->getArrayCopy(),
                                $this->sayDateTime($channelId, $timestamp, 'A', "$playbackId-$i")->getArrayCopy()));
                        } else {
                            $playbackList->exchangeArray(array_merge($playbackList->getArrayCopy(),
                                $this->sayDateTime($channelId, $timestamp, 'ABdY', "$playbackId-$i")->getArrayCopy()));
                        }
                    }
                    break;
                case 'S': // seconds
                    $sound = (int)$timestamp->format('s');
                    $playbackList[] = $this->playMedia($channelId, "number:$sound", null, null, null, "$playbackId-$i");
                    break;
            }

            $i++;
        }

        return $playbackList;
    }

    /**
     * Say a sound (audio file).
     *
     * @param $channelId
     * @param string $soundName
     * @param string $playbackId
     *
     * @return \phparia\Resources\Playback
     */
    public function saySound($channelId, $soundName, $playbackId = null)
    {
        $playback = $this->playMedia($channelId, "sound:$soundName", null, null, null, $playbackId);

        return $playback;
    }

}
