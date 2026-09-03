<div>
    <h1 class="text-2xl mb-10 flex justify-start items-center gap-2">{!! \CrudBooster\Components\Icon\Icon::KEY !!}
        Security</h1>
    <div class="frame">
        <div class="frame-title">
            Login Page
        </div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label>Whitelist IP</label>
                    <textarea class="form-control" placeholder="E.g: 1.1.1.1" wire:model="form.login_whitelist_ip" rows="1"></textarea>
                    <div class="form-help">
                        Separate multiple IP addresses with a comma. Leave empty to disable.
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row justify-start items-start">
                    <!-- option throttle login -->
                    <div class="form-group lg:!w-[300px]">
                        <label for="throttle_login">Throttle Login</label>
                        <input type="number" placeholder="E.g: 3" class="form-control !w-[100px]" id="throttle_login"
                               wire:model="form.login_throttle">
                        <div class="form-help">
                            Throttle login is a security feature that limits the number of login attempts per minute.
                        </div>
                    </div>

                    <div class="form-group lg:!w-[300px]">
                        <label for="throttle_blocked_duration">Throttle Blocked Duration (Login)</label>
                        <input type="number" placeholder="E.g: 15" class="form-control !w-[100px]"
                               id="throttle_blocked_duration" wire:model="form.login_throttle_blocked_duration">
                        <div class="form-help">
                            Throttle blocked duration is the time in minutes that a user is blocked from logging in
                            after reaching the throttle limit.
                        </div>
                    </div>
                </div>

                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="frame">
        <div class="frame-title">Forgot Password</div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <!-- Enable forgot password -->
                <div class="form-group">
                    <label for="enable_forgot_password">Enable Forgot Password</label>
                    <x-toggle-button id="toggle" model="form.forgot_status"/>
                    <div class="form-help">
                        Enable forgot password feature.
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row justify-start items-start">

                    <div class="form-group lg:!w-[300px]">
                        <label for="throttle_forgot">Throttle Forgot Password</label>
                        <input type="number" placeholder="E.g: 3" class="form-control !w-[100px]" id="throttle_forgot"
                               wire:model="form.forgot_throttle">
                        <div class="form-help">
                            Throttle login is a security feature that limits the number of forgot attempts per minute.
                        </div>
                    </div>

                    <div class="form-group lg:!w-[300px]">
                        <label for="throttle_blocked_duration_forgot">Throttle Blocked Duration (Forgot
                            Password)</label>
                        <input type="number" placeholder="E.g: 15" class="form-control !w-[100px]"
                               id="throttle_blocked_duration_forgot" wire:model="form.forgot_throttle_blocked_duration">
                        <div class="form-help">
                            Throttle blocked duration is the time in minutes that a user is blocked from forgot after
                            reaching the throttle limit.
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
