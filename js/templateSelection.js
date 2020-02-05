cj(document).ready(
    function ()
    {
        class TemplateFile
        {
            /**
             * @type string
             */
            id;
            /**
             * @type string
             */
            title;
        }

        class TemplateTree
        {
            /**
             * @type string
             */
            title;
            /**
             * @type TemplateFile[]
             */
            files;
            /**
             * @type TemplateTree[]
             */
            folders;
        }

        /** @type HTMLUListElement */
        const templateTreesElement = document.getElementById('templateTrees');

        /** @type HTMLTemplateElement */
        const htmlTreeTemplate = document.getElementById('htmlTreeTemplate');
        /** @type HTMLTemplateElement */
        const htmlFileEntryTemplate = document.getElementById('htmlFileEntryTemplate');

        /** @type TemplateTree[] */
        const templateTrees = CRM.vars.onlyoffice.templateTrees;

        console.log(templateTrees);

        for (const templateTree of templateTrees)
        {
            createFolderList(templateTreesElement, templateTree);
        }

        JSLists.createTree('templateTrees');

        /**
         * Create a new list as a sub node of an existing one.
         * @param {HTMLUListElement} parent The parent list element the new list shall be attached to.
         * @param {TemplateTree} templateTree The TemplateTree of the list that shall be created.
         */
        function createFolderList(parent, templateTree)
        {
            /** @type DocumentFragment */
            const listFragment = htmlTreeTemplate.content.cloneNode(true);

            /** @type HTMLLIElement */
            const listContainer = listFragment.firstElementChild;

            /** @type HTMLDivElement */
            const titleDiv = listContainer.firstElementChild;

            titleDiv.textContent = templateTree.title;

            /** @type HTMLUListElement */
            const list = listContainer.lastElementChild;

            parent.appendChild(listFragment);

            for (const folder of templateTree.folders)
            {
                createFolderList(list, folder);
            }

            for (const file of templateTree.files)
            {
                createFileEntry(list, file);
            }
        }

        /**
         * @param {HTMLUListElement} parent
         * @param {TemplateFile} templateFile
         */
        function createFileEntry(parent, templateFile)
        {
            /** @type DocumentFragment */
            const entryFragment = htmlFileEntryTemplate.content.cloneNode(true);

            /** @type HTMLLIElement */
            const entryContainer = entryFragment.firstElementChild;

            /** @type HTMLInputElement */
            const radioButton = entryContainer.firstElementChild;

            radioButton.value = templateFile.id;

            /** @type HTMLSpanElement */
            const titleSpan = entryContainer.lastElementChild;

            titleSpan.textContent = templateFile.title;

            parent.appendChild(entryFragment);
        }
    }
);
