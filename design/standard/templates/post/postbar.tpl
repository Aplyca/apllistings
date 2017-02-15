 <b>
 {if $steps.type}
 <a href="/post/type">Type</a>
 {/if}
 {if $steps.edit}
  <a href="{if $modulename|eq(listingactions)}/listingactions/edit/{$id}{else}/post/edit{/if}">Edit</a>
 {/if}
 {if $steps.package}
  <a href="/post/package">Package</a>
 {/if}
  {if $steps.publish}
  <a href="/listingactions/publish/{$id}">Publish</a>
 {/if}
 </b>
 <br/> <br/>
 
