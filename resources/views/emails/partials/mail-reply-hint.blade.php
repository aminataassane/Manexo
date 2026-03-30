@php
    $mode = $mailReplyMode ?? 'use_link';
    $hintAlign = $mailReplyHintAlign ?? 'left';
@endphp
<tr>
    <td align="{{ $hintAlign }}" style="padding-top:12px; font-size:11px; line-height:1.55; color:#6b7280; text-align:{{ $hintAlign }};">
        @if ($mode === 'reply_email')
            {{ __('emails.reply_hint_email') }}
        @elseif ($mode === 'no_reply')
            {{ __('emails.reply_hint_no_reply') }}
        @else
            {{ __('emails.reply_hint_use_link') }}
        @endif
    </td>
</tr>
