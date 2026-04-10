

import { debounce } from 'underscore';
import SettingsForm from './components/SettingsForm.vue';
import axios from 'axios';

Statamic.booting(() => {
    Statamic.$components.register('settings-form', SettingsForm);

    Statamic.$bard.addExtension(({ tiptap }) => {
        const { Extension } = tiptap.core;
        let dropdownVisible = false;
        let lastWord = '';
        const currentUser = Statamic.user;

        if (!(currentUser.is_marketeer || currentUser.super)) {
            return;
        }

        const removeDropdown = () => {
            const existingDropdown = document.querySelector('.bard-suggestions-dropdown');

            if (existingDropdown) {
                existingDropdown.remove();
            }

            dropdownVisible = false;
        };

        document.addEventListener('click', (clickEvent) => {
            if (!clickEvent.target.closest('.bard-suggestions-dropdown')) {
                removeDropdown();
            }
        });

        const showDropdown = (coordinates, suggestions, onSelectSuggestion) => {
            removeDropdown();

            const dropdown = document.createElement('div');
            dropdown.className = 'bard-suggestions-dropdown';
            dropdown.style.position = 'absolute';
            dropdown.style.left = `${coordinates.left}px`;
            dropdown.style.top = `${coordinates.top}px`;
            dropdown.style.background = 'white';
            dropdown.style.border = '1px solid #ccc';
            dropdown.style.padding = '5px';
            dropdown.style.zIndex = 9999;
            dropdown.style.maxHeight = '350px';
            dropdown.style.overflowY = 'scroll';

            suggestions.forEach((suggestion) => {
                const option = document.createElement('div');
                option.textContent = typeof suggestion === 'string'
                    ? `${suggestion} - Sitemap`
                    : `${suggestion.title} - Statamic`;
                option.className = 'cursor-pointer px-4 py-2 text-sm hover:bg-gray-200 dark:hover:bg-dark-400';

                option.addEventListener('click', () => {
                    onSelectSuggestion(suggestion);
                    removeDropdown();
                });

                dropdown.appendChild(option);
            });

            document.body.appendChild(dropdown);
            dropdownVisible = true;
        };

        const insertSuggestion = (editor, word, suggestion) => {
            editor.chain().focus().command(({ tr: transaction, state, dispatch }) => {
                const linkMark = state.schema.marks.link;

                if (!linkMark) {
                    return false;
                }

                const selectionStart = transaction.selection.from;
                const textBeforeSelection = transaction.doc.textBetween(0, selectionStart, ' ');
                const latestWordAtSelection = textBeforeSelection.split(/\s+/).pop() || '';
                const replacementLength = word && latestWordAtSelection.endsWith(word)
                    ? word.length
                    : latestWordAtSelection.length;
                const replacementStart = selectionStart - replacementLength;
                const replacementEnd = selectionStart;
                const url = typeof suggestion === 'string' ? suggestion : suggestion.url;

                if (typeof suggestion !== 'string' && !suggestion.url) {
                    Statamic.$toast.error('The selected entry does not have a url.');

                    return false;
                }

                transaction.insertText(word, replacementStart, replacementEnd);
                transaction.addMark(
                    replacementStart,
                    replacementStart + word.length,
                    linkMark.create({ href: url }),
                );

                if (dispatch) {
                    dispatch(transaction);
                }

                return true;
            }).run();
        };

        const getLatestWord = (textContent, cursorPosition) => {
            const textBeforeCursor = textContent.slice(0, cursorPosition);
            const words = textBeforeCursor.trim().split(/\s+/);

            return words[words.length - 1] || '';
        };

        const fetchSuggestions = debounce(async (editor) => {
            const cursorPosition = editor.state.selection.from;

            const latestWordAtCursor = getLatestWord(editor.getText(), cursorPosition);

            if (latestWordAtCursor === lastWord || latestWordAtCursor.length < 3) {
                return;
            }

            editor.view.dom.classList.add('is-loading')

            lastWord = latestWordAtCursor;

            const response = await axios.post('/cp/api/suggestions', {
                query: latestWordAtCursor,
            });
            const suggestions = response.data;

            if (!Array.isArray(suggestions) || suggestions.length === 0) {
                removeDropdown();

                return;
            }

            const selectionPosition = editor.state.selection.from;
            const coordinates = editor.view.coordsAtPos(selectionPosition);

            showDropdown(coordinates, suggestions, (entry) => {
                insertSuggestion(editor, latestWordAtCursor, entry);
            });
            
            editor.view.dom.classList.remove('is-loading')
        }, 1000);

        return Extension.create({
            name: 'suggestions',
            onUpdate: async ({ editor }) => {
                await fetchSuggestions(editor);
            },
            addProseMirrorPlugins: () => {
                return [];
            },
        });
    });
});
