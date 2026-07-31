<?php

namespace App\Http\Requests\CarWashPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveWeeklyScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('carwash.schedule.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'days' => ['required', 'array', 'size:7'],
            'days.*.enabled' => ['required', 'boolean'],
            'days.*.start_time' => ['nullable', 'required_if:days.*.enabled,1', 'date_format:H:i'],
            'days.*.end_time' => ['nullable', 'required_if:days.*.enabled,1', 'date_format:H:i'],
            'days.*.slot_duration_minutes' => ['nullable', 'required_if:days.*.enabled,1', Rule::in([30, 60])],
            'days.*.capacity' => ['nullable', 'required_if:days.*.enabled,1', 'integer', 'min:1', 'max:100'],
            'days.*.slot_capacities' => ['nullable', 'array'],
            'days.*.slot_capacities.*' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            foreach ((array) $this->input('days', []) as $weekday => $day) {
                if (! filter_var($day['enabled'] ?? false, FILTER_VALIDATE_BOOL)) {
                    continue;
                }

                $start = $this->minutes((string) ($day['start_time'] ?? ''));
                $end = $this->minutes((string) ($day['end_time'] ?? ''));
                $duration = (int) ($day['slot_duration_minutes'] ?? 0);

                if ($start === null || $end === null || $end <= $start) {
                    $validator->errors()->add("days.{$weekday}.end_time", 'ساعت پایان باید بعد از ساعت شروع باشد.');
                    continue;
                }

                if ($duration > 0 && ($end - $start) % $duration !== 0) {
                    $validator->errors()->add(
                        "days.{$weekday}.end_time",
                        'فاصله شروع تا پایان باید دقیقاً بر مدت اسلات بخش‌پذیر باشد.'
                    );
                }

                if ($duration > 0 && intdiv($end - $start, $duration) > 32) {
                    $validator->errors()->add("days.{$weekday}.start_time", 'تعداد اسلات‌های یک روز بیش از حد مجاز است.');
                }
            }
        }];
    }

    private function minutes(string $time): ?int
    {
        if (! preg_match('/^(\d{2}):(\d{2})$/', $time, $matches)) {
            return null;
        }

        return ((int) $matches[1] * 60) + (int) $matches[2];
    }
}
