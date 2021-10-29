    <div class="row mt-4" id="replytopportunityupdate{{$update->id}}form" style="display: none;">
         <form method="post" action="#" autocomplete="off" class="mt--3">
                         @csrf
        <div class="row">
     <input type="hidden" name="opportunity_id" value="{{$opportunity->id}}">
     <input type="hidden" name="opportunity_update_id" value="{{$update->id}}">
     <input type="hidden" name="user_id" value="{{loginUserId()}}">
                                
                          </div>
                        <div class="row">
                                    <div class="col-md-12" style="width: 600px;">
                                        <div class="form-group{{ $errors->has('reply') ? ' has-danger' : '' }}">

                                            <textarea class="form-control" name="reply"  placeholder="Type a reply" rows="4" required></textarea>
                                        </div>
                                    </div>
                                </div>
            <div class="text-center">
    <button type="button" onclick="replyOpportunityUpdate({{$update->id}})" class="btn btn-warning">{{ __('Cancel') }}</button>

    <button type="submit" class="btn btn-success" id="submitRenewalButton">{{ __('Save') }}</button>

  </div>
</form>
    </div>

    <script type="text/javascript">

    </script>