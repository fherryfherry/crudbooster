<div>
    <h1 class="text-2xl mb-10 flex justify-start items-center gap-2">{!! \CrudBooster\Components\Icon\Icon::BUILDING !!} Basic Information</h1>
    <div class="frame">
        <div class="frame-title">
            Basic Information
        </div>
        <div class="frame-content">
            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label for="">App Name</label>
                    <input type="text" wire:model="form.app_name" class="form-control w-full lg:!w-1/2">
                    <div class="form-help">
                        This is the name of your application
                    </div>
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                        <input type="text" wire:model="form.company_name" placeholder="E.g: AI Company" class="form-control w-full lg:!w-1/2">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" wire:model="form.address" class="form-control">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" wire:model="form.phone" class="form-control w-full lg:!w-1/3">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" wire:model="form.email" class="form-control w-full lg:!w-1/3" placeholder="E.g: email@example.com">
                </div>
                <div class="flex flex-row justify-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
