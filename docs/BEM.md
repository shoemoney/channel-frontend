# BEM naming convention

## Why we use BEM
We're coding CSS with a general adherence to a BEM syntax. I could talk for hours about why this is good, and I've done more than one tech Tuesday on the topic, but I'll try to keep this brief. There are huge developer benefits to writing CSS in this way in projects of scale. The general idea is that if you can use just a single class name as a selector, you immediately bypass the complications and headaches of the Cascading nature of CSS and the [system of precedence](https://specificity.keegan.st/) which determines how rules override each other.

If all selectors have the roughly the same precedence, authoring CSS becomes more predictable, and having a convention that consistently describes the kind of selector being styled helps other developers to understand the context in which the target element exists very easily.

## What is BEM?
BEM essentially defines a naming convention and three conceptual levels of structure that aims to help make the classes you author more meaningful, predictable, and less prone to errors. These three levels of the BEM acronym are described below.

## (B)locks
Blocks are anything that can be thought of as a component, or something that exists only in a general context, and not as a tightly coupled child of its parent. It doesn't need to have child elements, but it could.

## (E)lements
An element is some descendent of a block. When written as a class name it must include the name of the Block and the name of the Element with a double underscore (`__`). Below is an example of a class name written an element:

`.search-result__image` for
```
  <div class="search-result">
    <div class="search-result__image">
      <img src="" alt="" />
    </div>
    <div class="search-result__text">
      <p>...</p>
    </div>
  </div>
```

Note this is all one class name. You may use terse, expressive names for Blocks, Elements or Modifiers because they will always combine into a singular, unique class reference a very specific 'thing'. Also note that there isn't a restriction on having multiple Elements in a BEM class, although this is uncommon. Decide based on how likely it is to conflict with deeper nested elements if this is appropriate.

## (M)odifier
Is used to describe a variation on a Block or Element. A modifier is written with a double hyphen (`--`). When using modifier classes, they are applied in the HTML in addition to the generic unmodified class. For this reason styles applied to modifiers need only describe the differences between itself and the generic Block or Element, because the generic styles will be inherited and selectively overridden.

Maybe our typical search result from before has text and an image, and sometimes we have a type of result without an image. A modifier can describe this:

```
<div class="search-result">
  <div class="search-result__image">
    <img src="" alt="">
  </div>
  <div class="search-result__text">
    <p>...</p>
  </div>
</div>
<div class="search-result search-result--wide">
  <div class="search-result__text">
    <p>...</p>
  </div>
</div>
```

Here I add the modifier to the block, because I intend to style this differently. Alternatively I could add a modifier to the text making it `search-result__text search-result__text--wide`.

## Good BEM vs Bad BEM

### Good BEM
- `.viewer__thumbnails`
- `.saved-sets`
- `.icon--zoom`

### Bad BEM
- `.viewer.thumbnails` - Not one class name
- `.viewer .viewer__thumbnail` - '.viewer' is unnecessary
- `icon.icon--zoom` - Not one class name and '.icon' is unnecessary
- `.imageViewer`, `image_viewer` - Only use lowercase letters and kebab-case (i.e. use hyphens for word separation)
