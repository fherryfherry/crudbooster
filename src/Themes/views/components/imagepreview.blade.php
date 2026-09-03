<div id="modal" x-cloak x-show="openThumbnail" class="thumbnail-preview">
    <a class="close" href="javascript:void(0)" @click="openThumbnail = false">×</a>
    <div class="flex flex-col justify-center items-center gap-4">
        <img :src="thumbnailSrc" @click.away="openThumbnail = false" class="preview" />
        <span x-text="thumbnailCaption" class="text-white font-bold"></span>
    </div>
</div>
