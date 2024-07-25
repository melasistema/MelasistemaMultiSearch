import Plugin from "src/plugin-system/plugin.class";

export default class MultiSearchPlugin extends Plugin {

  /**
   *
   * init
   */
  init() {
    const searchModal = document.getElementById('multiSearchModal');
    const keywordsTextArea = searchModal.querySelector('textarea[name="multiSearchKeywords"]');
    const tabsContainer = document.querySelector('.multi-search-tabs-wrapper');

    if (tabsContainer)
    {
      this.updateTabsBasedOnLocalStorage(tabsContainer);
    }

    if (searchModal) {
      this.populateTextAreaBasedOnLocalStorage(keywordsTextArea);
      this.submitMultiSearch(searchModal);
    }
  }

  /**
   *
   * @param tabsContainer
   */
  updateTabsBasedOnLocalStorage(tabsContainer) {

    const storedKeywords = localStorage.getItem('multiSearchKeywords');

    if (storedKeywords) {
      const keywordsArray = storedKeywords.split('|');

      // Create the unordered list
      const ul = document.createElement('ul');

      // Clear the existing tabs container
      tabsContainer.innerHTML = '';

      keywordsArray.forEach(keyword => {
        const tabItem = document.createElement('li');
        const tabLink = document.createElement('a');
        const tabButton = document.createElement('button');

        // Add the 'is-active' class if the keyword matches the active term
        const activeTerm = this.getActiveTermFromUrl();
        if (keyword === activeTerm) {
          tabButton.classList.add('is-active');
        }

        tabLink.href = `multi-search?search=${keyword}`; // Replace with your desired URL structure
        tabButton.textContent = keyword;

        tabLink.classList.add('multi-search-link');
        tabButton.classList.add('btn', 'btn-outline-secondary');

        tabLink.appendChild(tabButton);
        tabItem.appendChild(tabLink);
        ul.appendChild(tabItem);
      });

      tabsContainer.appendChild(ul);

    }
  }

  /**
   *
   * @param keywordsTextArea
   */
  populateTextAreaBasedOnLocalStorage(keywordsTextArea) {
    const storedKeywords = localStorage.getItem('multiSearchKeywords');
    if (storedKeywords) {
      // Split keywords separated by pipes into an array
      const keywordsArray = storedKeywords.split('|');
      // Join the array elements with newlines
      keywordsTextArea.value = keywordsArray.join('\n');
    }
  }

  /**
   *
   * @param searchModal
   */
  submitMultiSearch(searchModal) {

    const form = document.querySelector('#multiSearchModal form');

    form.addEventListener('submit', (event) => {
      // Prevent default form submission behavior
      event.preventDefault();

      // Gather the multiSearchKeywords
      const keywordsTextArea = searchModal.querySelector('textarea[name="multiSearchKeywords"]');
      const keywords = keywordsTextArea.value.trim(); // Trim leading/trailing whitespace

      /**
       *
       * Return an Alert if no keywords are present!
       * TODO implement snippets or better a proper validation
       */
      if (keywords) {

        // Prepare keywords with pipe separation
        const pipeSeparatedKeywords = keywords.replace(/\n/g, '|');
        // Store keywords in localStorage (optional, for persistence across page loads)
        localStorage.setItem('multiSearchKeywords', pipeSeparatedKeywords);
        // Create a hidden form field to submit keywords
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'allKeywords';
        hiddenInput.value = pipeSeparatedKeywords;
        // Append the hidden field to the form
        form.appendChild(hiddenInput);
        // Submit the multi search form
        form.submit();

      } else {
        alert( 'The Multi Search require at least one keyword!' );
      }

    });
  }

  /**
   *
   * @returns {string}
   */
  getActiveTermFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('search');
  }

  /**
   *
   * @param firstKeyword
   * @returns {string}
   */
  generateUrl(firstKeyword) {
    const url = new URL('/multi-search', window.location.origin);
    url.searchParams.set('search', firstKeyword);
    return url.toString();
  }

}
