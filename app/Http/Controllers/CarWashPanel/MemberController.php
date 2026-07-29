<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use App\Http\Requests\CarWashPanel\InviteMemberRequest;
use App\Models\CarWash;
use App\Models\User;
use App\Services\CarWashInvitationService;
use App\Services\CarWashMemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(CarWash $carWash): View
    {
        return view('carwash.members.index', [
            'carWash' => $carWash,
            'members' => $carWash->members()->paginate(25),
            'invitations' => $carWash->invitations()
                ->latest()
                ->paginate(25, ['*'], 'invitations'),
        ]);
    }

    public function invite(
        InviteMemberRequest $request,
        CarWash $carWash,
        CarWashInvitationService $service,
    ): RedirectResponse {
        $role = RoleName::from($request->string('role')->toString());

        $invitation = $service->create(
            carWash: $carWash,
            inviter: $request->user(),
            role: $role,
            mobile: $request->validated('mobile'),
            email: $request->validated('email'),
        );

        return back()->with([
            'success' => 'دعوت ساخته شد.',
            'invitation_url' => route('invitations.show', $invitation['token']),
        ]);
    }

    public function updateRole(
        Request $request,
        CarWash $carWash,
        User $member,
        CarWashMemberService $service,
    ): RedirectResponse {
        $this->ensureMemberBelongsToCarWash($carWash, $member);

        $data = $request->validate([
            'role' => ['required', Rule::in(RoleName::carWashValues())],
        ]);

        $role = RoleName::from($data['role']);

        if (
            ! $request->user()->is_super_admin
            && $role === RoleName::CAR_WASH_OWNER
        ) {
            abort(422, 'اختصاص نقش مالک فقط توسط مدیر کل سیستم امکان‌پذیر است.');
        }

        if (
            ! $request->user()->is_super_admin
            && $member->hasRole(RoleName::CAR_WASH_OWNER->value)
        ) {
            abort(422, 'تغییر نقش مالک فقط توسط مدیر کل سیستم امکان‌پذیر است.');
        }

        $service->addOrUpdate(
            $carWash,
            $member,
            $role,
            $request->user(),
        );

        return back()->with('success', 'نقش عضو به‌روزرسانی شد.');
    }

    public function destroy(
        Request $request,
        CarWash $carWash,
        User $member,
        CarWashMemberService $service,
    ): RedirectResponse {
        $this->ensureMemberBelongsToCarWash($carWash, $member);

        abort_if(
            $member->is($request->user()),
            422,
            'امکان حذف حساب خودتان وجود ندارد.',
        );

        if (
            ! $request->user()->is_super_admin
            && $member->hasRole(RoleName::CAR_WASH_OWNER->value)
        ) {
            abort(422, 'حذف مالک فقط توسط مدیر کل سیستم امکان‌پذیر است.');
        }

        $service->remove($carWash, $member);

        return back()->with('success', 'عضو از کارواش حذف شد.');
    }

    private function ensureMemberBelongsToCarWash(
        CarWash $carWash,
        User $member,
    ): void {
        abort_unless(
            $carWash->members()->whereKey($member->getKey())->exists(),
            404,
        );
    }
}
