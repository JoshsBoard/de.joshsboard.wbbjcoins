{if MODULE_JCOINS}
	<fieldset id="jcoinsContainer">
		<legend>{lang}wbb.acp.board.jcoins{/lang}</legend>

		<dl>
			<dd>
				<label><input type="checkbox" id="customJCoins" name="customJCoins" value="1"{if $customJCoins} checked="checked"{/if} /> {lang}wbb.acp.board.jcoins.custom{/lang}</label>
			</dd>
		</dl>

		<dl class="customJCoinsOption">
			<dt><label for="customJCoinsCreateThread">{lang}wbb.acp.board.jcoins.thread.customval{/lang}</label></dt>
			<dd>
				<input type="number" id="customJCoinsCreateThread" name="customJCoinsCreateThread" value="{$customJCoinsCreateThread}" class="tiny" min="0" />
				<small>{lang}wbb.acp.board.jcoins.thread.customval.description{/lang}</small>
			</dd>
		</dl>

		<dl class="customJCoinsOption">
			<dt><label for="customJCoinsTrashThread">{lang}wbb.acp.board.jcoins.trashedthread.customval{/lang}</label></dt>
			<dd>
				<input type="number" id="customJCoinsTrashThread" name="customJCoinsTrashThread" value="{$customJCoinsTrashThread}" class="tiny" min="0" />
				<small>{lang}wbb.acp.board.jcoins.trashedthread.customval.description{/lang}</small>
			</dd>
		</dl>

		<dl class="customJCoinsOption">
			<dt><label for="customJCoinsCreatePost">{lang}wbb.acp.board.jcoins.post.customval{/lang}</label></dt>
			<dd>
				<input type="number" id="customJCoinsCreatePost" name="customJCoinsCreatePost" value="{$customJCoinsCreatePost}" class="tiny" min="0" />
				<small>{lang}wbb.acp.board.jcoins.post.customval.description{/lang}</small>
			</dd>
		</dl>

		<dl class="customJCoinsOption">
			<dt><label for="customJCoinsTrashPost">{lang}wbb.acp.board.jcoins.trashedpost.customval{/lang}</label></dt>
			<dd>
				<input type="number" id="customJCoinsTrashPost" name="customJCoinsTrashPost" value="{$customJCoinsTrashPost}" class="tiny" min="0" />
				<small>{lang}wbb.acp.board.jcoins.trashedpost.customval.description{/lang}</small>
			</dd>
		</dl>

		{event name='jcoinsBoardField'}
	</fieldset>

	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			function toogleJCoinsContainer(bool) {
				if (bool) {
					$('.customJCoinsOption').show(); 
				} else {
					$('.customJCoinsOption').hide(); 
				}
			}

			toogleJCoinsContainer({@$customJCoins});
		});
		//]]>
	</script>
{/if}